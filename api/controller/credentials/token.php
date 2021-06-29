<?php

use Firebase\JWT\JWT;

class ControllerCredentialsToken extends Controller {

	private const EXPIRE = 3600;

	private const HTTP_STATUS_401 = 'HTTP/1.1 401 Unauthorized';

	public function index() {
		$this->load->model('credentials/token');

		$json = [];

		if (!isset($this->request->headers['authorization'])) {
			return new Action('status_code/unauthorized');
		}

		$authorization = $this->request->headers['authorization'];

		@list($token_type, $credentials) = explode(' ', $authorization);

		if (strtolower($token_type) !== 'basic') {
			return new Action('status_code/bad_request');
		}

		if (empty($credentials)) {
			return new Action('status_code/bad_request');
		}

		$credentials_decoded = base64_decode($credentials);

		if (!preg_match('/^[a-z]+:[a-z]+$/i', $credentials_decoded)) {
			return new Action('status_code/bad_request');
		}

		list($client_id, $client_secret) = explode(':', $credentials_decoded);

		$logged = $this->model_credentials_token->login($client_id, $client_secret);

		if ($logged === false) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					'code' => 'invalid_credential',
					'message' => 'Credentials are invalid.'
				)
			)));
			return new Action('status_code/unauthorized');
		}

		$time = time();

		$jti_hash = sprintf('%s:%s', $client_id, microtime(true));
		$jti = hash_hmac('sha256', $jti_hash, $client_secret);

		$payload = array(
			'iss' => $this->config->get('config_url'),
			'iat' => $time,
			'sub' => '<username here>', /** @todo Integrar ao banco de dados */
			'exp' => $time + self::EXPIRE,
			'jti' => $jti,
			'application_name' 	=> 'V5Market', /** @todo Integrar ao banco de dados */
		);

		$jwt = JWT::encode($payload, $this->config->get('secret_key'));

		$json = [
			'access_token' 		=> (string)$jwt,
			'token_type' 		=> 'Bearer',
			'expires_in' 		=> self::EXPIRE - 1,
		];

		$this->response->setOutput(json_encode($json));
	}
}
