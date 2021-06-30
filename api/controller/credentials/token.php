<?php

use Firebase\JWT\JWT;

class ControllerCredentialsToken extends Controller {

	private const EXPIRE = 3600;

	public function index() {
		$this->load->model('credentials/token');

		$json = [];

		if (!isset($this->request->headers['authorization'])) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'forgotten_authorization',
						'message' => 'It is necessary to inform the authorization header.'
					)
				)
			)));
			return new Action('status_code/unauthorized');
		}

		$authorization = $this->request->headers['authorization'];

		@list($token_type, $credentials) = explode(' ', $authorization);

		if (strtolower($token_type) !== 'basic') {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_authorization_type',
						'message' => 'It is necessary to inform the type "Basic" in the authentication header.'
					)
				)
			)));
			return new Action('status_code/bad_request');
		}

		if (empty($credentials)) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'forgotten_credentials',
						'message' => 'It is necessary to inform the credentials.'
					)
				)
			)));
			return new Action('status_code/bad_request');
		}

		$credentials_decoded = base64_decode($credentials);

		if (!preg_match('/^[a-z0-9]+:[a-z0-9]+$/i', $credentials_decoded)) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_credential_format',
						'message' => 'The credential must be "client_id:client_secret" encoded with base64.'
					)
				)
			)));
			return new Action('status_code/bad_request');
		}

		list($client_id, $client_secret) = explode(':', $credentials_decoded);

		$logged = $this->model_credentials_token->login($client_id, $client_secret);

		if ($logged === false) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_credential',
						'message' => 'Credentials are invalid.'
					)
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
