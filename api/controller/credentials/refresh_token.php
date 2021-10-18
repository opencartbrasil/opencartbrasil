<?php
use Firebase\JWT\JWT;

class ControllerCredentialsRefreshToken extends Controller {
	private const EXPIRE = 3600;
	private const REFRESH_EXPIRE = 86400;

	/**
	 * Generate new access token
	 */
	public function index() {
		$this->load->model('credentials/token');

		$json = array();

		$isValidToken = false;

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
			return $this->responseRefreshTokenFailed();
		}

		try {
			$token = $this->model_credentials_token->generateToken($body_decoded->sub, self::REFRESH_EXPIRE);

			$json = array(
				'access_token' => (string)$token['jwt'],
				'token_type' => 'Bearer',
				'expires_in' => self::REFRESH_EXPIRE - 1
			);

			$this->model_credentials_token->addToken($body_decoded->sub, $token['jwt'], $refresh_token);

			$this->model_credentials_token->addHistory($body_decoded->sub, 'refresh_token');

			$this->response->setOutput(json_encode($json));
		} catch (UnexpectedValueException $ignored) {
			return $this->responseRefreshTokenFailed();
		}
	}

	/**
	 * Write the fault response and return the controller that corresponds to the error
	 *
	 * @return Action
	 */
	private function responseRefreshTokenFailed() {
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

	/**
	 * Validate request
	 *
	 * @return Action|string Return Action when there is a validation error
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
