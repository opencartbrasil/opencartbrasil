<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Mail class
*/
class Mail {
	protected $to;
	protected $from;
	protected $sender;
	protected $reply_to;
	protected $subject;
	protected $text;
	protected $html;
	protected $attachments = array();
	public $parameter;

	/**
	 * Constructor
	 *
	 * @param	string	$adaptor
	 *
	*/
	public function __construct($adaptor = 'mail') {
		$class = 'Mail\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class();
		} else {
			trigger_error('Erro: Não foi possível carregar o adaptador de e-mail ' . $adaptor . '!');
			exit();
		}	
	}

	/**
	 *
	 *
	 * @param	mixed	$to
	 */
	public function setTo($to) {
		$this->to = $to;
	}

	/**
	 *
	 *
	 * @param	string	$from
	 */
	public function setFrom($from) {
		$this->from = $from;
	}

	/**
	 *
	 *
	 * @param	string	$sender
	 */
	public function setSender($sender) {
		$this->sender = $sender;
	}

	/**
	 *
	 *
	 * @param	string	$reply_to
	 */
	public function setReplyTo($reply_to) {
		$this->reply_to = $reply_to;
	}

	/**
	 *
	 *
	 * @param	string	$subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 *
	 *
	 * @param	string	$text
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 *
	 *
	 * @param	string	$html
	 */
	public function setHtml($html) {
		$this->html = $html;
	}

	/**
	 *
	 *
	 * @param	string	$filename
	 */
	public function addAttachment($filename) {
		$this->attachments[] = $filename;
	}

	/**
	 *
	 *
	 */
	public function send() {
		try {
			if (!$this->to) {
				throw new \Exception('Erro: O e-mail do destinatário é obrigatório!');
			}

			if (!$this->from) {
				throw new \Exception('Erro: O e-mail do remetente é obrigatório!');
			}

			if (!$this->sender) {
				throw new \Exception('Erro: O nome do remetente é obrigatório!');
			}

			if (!$this->subject) {
				throw new \Exception('Erro: O assunto do e-mail é obrigatório!');
			}

			if ((!$this->text) && (!$this->html)) {
				throw new \Exception('Erro: A mensagem do e-mail é obrigatória!');
			}

			foreach (get_object_vars($this) as $key => $value) {
				$this->adaptor->$key = $value;
			}

			$this->adaptor->send();
		} catch (\Exception $e) {
			$logger = new \Log("mail.log");
			$logger->write($e->getMessage());
		}
	}
}