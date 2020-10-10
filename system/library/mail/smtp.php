<?php
namespace Mail;
class Smtp {
	public $smtp_hostname;
	public $smtp_username;
	public $smtp_password;
	public $smtp_port = 25;
	public $smtp_timeout = 5;
	public $max_attempts = 3;
	public $verp = false;

	public function send() {
		if (is_array($this->to)) {
			$to = implode(',', $this->to);
		} else {
			$to = $this->to;
		}

		$boundary = '----=_NextPart_' . md5(time());

		$header = 'MIME-Version: 1.0' . PHP_EOL;
		$header .= 'To: <' . $to . '>' . PHP_EOL;
		$header .= 'Subject: =?UTF-8?B?' . base64_encode($this->subject) . '?=' . PHP_EOL;
		$header .= 'Date: ' . date('D, d M Y H:i:s O') . PHP_EOL;
		$header .= 'From: =?UTF-8?B?' . base64_encode($this->sender) . '?= <' . $this->from . '>' . PHP_EOL;

		if (!$this->reply_to) {
			$header .= 'Reply-To: =?UTF-8?B?' . base64_encode($this->sender) . '?= <' . $this->from . '>' . PHP_EOL;
		} else {
			$header .= 'Reply-To: =?UTF-8?B?' . base64_encode($this->reply_to) . '?= <' . $this->reply_to . '>' . PHP_EOL;
		}

		$header .= 'Return-Path: ' . $this->from . PHP_EOL;
		$header .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
		$header .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . PHP_EOL . PHP_EOL;

		if (!$this->html) {
			$message = '--' . $boundary . PHP_EOL;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . PHP_EOL;
			$message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
			$message .= $this->text . PHP_EOL;
		} else {
			$message = '--' . $boundary . PHP_EOL;
			$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . PHP_EOL . PHP_EOL;
			$message .= '--' . $boundary . '_alt' . PHP_EOL;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . PHP_EOL;
			$message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;

			if ($this->text) {
				$message .= $this->text . PHP_EOL;
			} else {
				$message .= 'Este é um e-mail em HTML e o seu cliente de email não suporta e-mail em HTML!' . PHP_EOL;
			}

			$message .= '--' . $boundary . '_alt' . PHP_EOL;
			$message .= 'Content-Type: text/html; charset="utf-8"' . PHP_EOL;
			$message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
			$message .= $this->html . PHP_EOL;
			$message .= '--' . $boundary . '_alt--' . PHP_EOL;
		}

		foreach ($this->attachments as $attachment) {
			if (file_exists($attachment)) {
				$handle = fopen($attachment, 'r');

				$content = fread($handle, filesize($attachment));

				fclose($handle);

				$message .= '--' . $boundary . PHP_EOL;
				$message .= 'Content-Type: application/octet-stream; name="' . basename($attachment) . '"' . PHP_EOL;
				$message .= 'Content-Transfer-Encoding: base64' . PHP_EOL;
				$message .= 'Content-Disposition: attachment; filename="' . basename($attachment) . '"' . PHP_EOL;
				$message .= 'Content-ID: <' . urlencode(basename($attachment)) . '>' . PHP_EOL;
				$message .= 'X-Attachment-Id: ' . urlencode(basename($attachment)) . PHP_EOL . PHP_EOL;
				$message .= chunk_split(base64_encode($content));
			}
		}

		$message .= '--' . $boundary . '--' . PHP_EOL;

		if (substr($this->smtp_hostname, 0, 3) == 'tls') {
			$hostname = substr($this->smtp_hostname, 6);
		} else {
			$hostname = $this->smtp_hostname;
		}

		$handle = fsockopen($hostname, $this->smtp_port, $errno, $errstr, $this->smtp_timeout);

		if (!$handle) {
			throw new \Exception('Erro: ' . $errstr . ' (' . $errno . ')');
		} else {
			if (substr(PHP_OS, 0, 3) != 'WIN') {
				socket_set_timeout($handle, $this->smtp_timeout, 0);
			}

			while ($line = fgets($handle, 515)) {
				if (substr($line, 3, 1) == ' ') {
					break;
				}
			}

			fputs($handle, 'EHLO ' . getenv('SERVER_NAME') . "\r\n");

			$reply = '';

			while ($line = fgets($handle, 515)) {
				$reply .= $line;

				//some SMTP servers respond with 220 code before responding with 250. hence, we need to ignore 220 response string
				if (substr($reply, 0, 3) == 220 && substr($line, 3, 1) == ' ') {
					$reply = '';

					continue;
				} else if (substr($line, 3, 1) == ' ') {
					break;
				}
			}

			if (substr($reply, 0, 3) != 250) {
				throw new \Exception('Erro: O servidor SMTP recusou a cláusula EHLO!');
			}

			if (substr($this->smtp_hostname, 0, 3) == 'tls') {
				fputs($handle, 'STARTTLS' . "\r\n");

				$this->handleReply($handle, 220, 'Erro: O servidor SMTP recusou o comando STARTTLS!');

				stream_socket_enable_crypto($handle, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
			}

			if (!empty($this->smtp_username) && !empty($this->smtp_password)) {
				fputs($handle, 'EHLO ' . getenv('SERVER_NAME') . "\r\n");

				$this->handleReply($handle, 250, 'Erro: O servidor SMTP recusou a cláusula EHLO!');

				fputs($handle, 'AUTH LOGIN' . "\r\n");

				$this->handleReply($handle, 334, 'Erro: O servidor SMTP recusou a autenticação!');

				fputs($handle, base64_encode($this->smtp_username) . "\r\n");

				$this->handleReply($handle, 334, 'Erro: O servidor SMTP recusou o usuário!');

				fputs($handle, base64_encode($this->smtp_password) . "\r\n");

				$this->handleReply($handle, 235, 'Erro: O servidor SMTP recusou a senha!');

			} else {
				fputs($handle, 'HELO ' . getenv('SERVER_NAME') . "\r\n");

				$this->handleReply($handle, 250, 'Erro: O servidor SMTP recusou a cláusula EHLO!');
			}

			if ($this->verp) {
				fputs($handle, 'MAIL FROM: <' . $this->smtp_username . '>XVERP' . "\r\n");
			} else {
				fputs($handle, 'MAIL FROM: <' . $this->smtp_username . '>' . "\r\n");
			}

			$this->handleReply($handle, 250, 'Erro: O servidor SMTP recusou o e-mail do remetente!');

			if (!is_array($this->to)) {
				fputs($handle, 'RCPT TO: <' . $this->to . '>' . "\r\n");

				$reply = $this->handleReply($handle, false, 'RCPT TO [!array]');

				if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
					throw new \Exception('Erro: O servidor SMTP recusou o e-mail do destinatário!');
				}
			} else {
				foreach ($this->to as $recipient) {
					fputs($handle, 'RCPT TO: <' . $recipient . '>' . "\r\n");

					$reply = $this->handleReply($handle, false, 'RCPT TO [array]');

					if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
						throw new \Exception('Erro: O servidor SMTP recusou o e-mail do destinatário!');
					}
				}
			}

			fputs($handle, 'DATA' . "\r\n");

			$this->handleReply($handle, 354, 'Erro: O servidor SMTP recusou receber o e-mail!');

			// According to rfc 821 we should not send more than 1000 including the CRLF
			$message = str_replace("\r\n", "\n", $header . $message);
			$message = str_replace("\r", "\n", $message);

			$length = (mb_detect_encoding($message, mb_detect_order(), true) == 'ASCII') ? 998 : 249;

			$lines = explode("\n", $message);

			foreach ($lines as $line) {
				$results = str_split($line, $length);

				foreach ($results as $result) {
					if (substr(PHP_OS, 0, 3) != 'WIN') {
						fputs($handle, $result . "\r\n");
					} else {
						fputs($handle, str_replace("\n", "\r\n", $result) . "\r\n");
					}
				}
			}

			fputs($handle, '.' . "\r\n");

			$this->handleReply($handle, 250, 'Erro: O servidor SMTP recusou enviar o e-mail!');

			fputs($handle, 'QUIT' . "\r\n");

			$this->handleReply($handle, 221, 'Erro: O servidor SMTP recusou o comando QUIT!');

			fclose($handle);
		}
	}

	private function handleReply($handle, $status_code = false, $error_text = false, $counter = 0) {
		$reply = '';

		while (($line = fgets($handle, 515)) !== false) {
			$reply .= $line;

			if (substr($line, 3, 1) == ' ') {
				break;
			}
		}

		// Handle slowish server responses (generally due to policy servers)
		if (!$line && empty($reply) && $counter < $this->max_attempts) {
			sleep(1);

			$counter++;

			return $this->handleReply($handle, $status_code, $error_text, $counter);
		}

		if ($status_code) {
			if (substr($reply, 0, 3) != $status_code) {
				throw new \Exception($error_text);
			}
		}

		return $reply;
	}
}
