<?php

use Firebase\JWT\JWT;

class ControllerCredentialsToken extends Controller {

	private const EXPIRE = 3600;
	private const EXPIRE_REFRESH_TOKEN = 86400;

	/**
	 * Cria token de acesso
	 */
	public function index() {
		$this->load->model('credentials/token');

		$json = [];

		$validate = $this->validate();

		if ($validate instanceof Action) {
			return $validate;
		}

		$authorization = $this->request->headers['authorization'];

		@list($token_type, $credentials) = explode(' ', $authorization);

		$credentials_decoded = base64_decode($credentials);

		list($client_id, $client_secret) = explode(':', $credentials_decoded);

		$user_id = $this->model_credentials_token->login($client_id, $client_secret);

		if ($user_id === false) {
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

		$access_token = $this->model_credentials_token->generateToken($user_id, self::EXPIRE);
		$refresh_token = $this->model_credentials_token->generateToken($user_id, self::EXPIRE_REFRESH_TOKEN);

		$this->model_credentials_token->addToken($user_id, $access_token['jwt'], $refresh_token['jwt'], $refresh_token['exp']);

		$json = [
			'access_token' 	=> (string)$access_token['jwt'],
			'refresh_token' => (string)$refresh_token['jwt'],
			'token_type' 	=> 'Bearer',
			'expires_in' 	=> self::EXPIRE - 1,
		];

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Valida requisição
	 *
	 * @return Action|string Retorna Action quando houver um erro de validação
	 */
	protected function validate() {
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

		$credentials_decoded = base64_decode($credentials);

		if (!preg_match('/^ck_[a-z0-9]+:cs_[a-z0-9]+$/i', $credentials_decoded)) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_credential_format',
						'message' => 'The credential must be "cs_consumer_key:ck_consumer_secret" encoded with base64.'
					)
				)
			)));
			return new Action('status_code/bad_request');
		}

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

		return $credentials;
	}
}
