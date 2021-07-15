<?php

use Firebase\JWT\JWT;

class ControllerCredentialsRefreshToken extends Controller {

	private const EXPIRE = 3600;

	/**
	 * Gera novo token de acesso
	 */
	public function index() {
		$this->load->model('credentials/token');

		$json = [];

		$isValidToken = false;
		$jwt_decoded = null;

		$validate = $this->validate();

		if ($validate instanceof Action) {
			return $validate;
		}

		$authorization = $this->request->headers['authorization'];

		try {
			@list($type, $refresh_token) = explode(' ', $authorization);
			$tks = \explode('.', $authorization);

			@list($head, $body) = $tks;

			$body_decoded = JWT::urlsafeB64Decode($body);
			$body_decoded = JWT::jsonDecode($body_decoded);

			$isValidToken = $this->model_credentials_token->refreshTokenIsValid($refresh_token, self::EXPIRE);
		} catch (ExpiredException $ignored) {
			$isValidToken = true;
		} catch (Exception $ignored) {
			$isValidToken = false;
		}

		if ($isValidToken === false) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'refresh_token_failed',
						'message' => 'Failed to update token.'
					)
				)
			)));
			return new Action('status_code/bad_request');
		}

		$this->model_credentials_token->disableRefreshToken($refresh_token);

		$jwt = $this->model_credentials_token->generateToken(1_000);

		$json = [
			'access_token' 	=> (string)$jwt,
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

		if (strtolower($token_type) !== 'bearer') {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_authorization_type',
						'message' => 'It is necessary to inform the type "Bearer" in the authentication header.'
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
