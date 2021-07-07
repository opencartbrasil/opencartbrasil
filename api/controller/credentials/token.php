<?php

use Firebase\JWT\JWT;

class ControllerCredentialsToken extends Controller {

	private const EXPIRE = 3600;

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

		$username = 'default'; 			/** @todo Integrar ao banco de dados */
		$application_name = 'V5Market';	/** @todo Integrar ao banco de dados */

		$json = $this->generateToken(
			$client_id,
			$client_secret,
			$username,
			$application_name
		);

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Realiza a atualização do token de acesso
	 */
	public function refresh() {
		$isValidToken = false;
		$jwt_decoded = null;

		$validate = $this->validate(false);

		if ($validate instanceof Action) {
			return $validate;
		}

		$authorization = $this->request->headers['authorization'];

		try {
			$tks = \explode('.', $authorization);

			@list($head, $body) = $tks;

			$body_decoded = JWT::urlsafeB64Decode($body);
			$body_decoded = JWT::jsonDecode($body_decoded);

			$isValidToken = true;
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

		$json = $this->generateToken(
			token(64),
			token(64),
			$body_decoded->sub,
			$body_decoded->application_name
		);

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Gera um novo token de acesso para o usuário
	 *
	 * @param string $client_id_or_random_key		ClientID do usuário ou um token aleatório em caso de atualização
	 * @param string $client_secret_or_random_key	ClientSecret do usuário ou um token aleatório em caso de atualização
	 * @param string $username						Nome do usuário para identificação
	 * @param string $application_name				Nome da aplicação para identificação
	 *
	 * @return string[]
	 */
	protected function generateToken(
		string $client_id_or_random_key,
		string $client_secret_or_random_key,
		string $username = '',
		string $application_name = ''
	) {
		$time = time();

		$jti_hash = sprintf('%s:%s', $client_id_or_random_key, microtime(true));
		$jti = hash_hmac('sha256', $jti_hash, $client_secret_or_random_key);

		$payload = array(
			'iss' => $this->config->get('config_url'),
			'iat' => $time,
			'sub' => $username,
			'exp' => $time + self::EXPIRE,
			'jti' => $jti,
			'application_name' 	=> $application_name,
		);

		$jwt = JWT::encode($payload, $this->config->get('secret_key'));

		return [
			'access_token' 	=> (string)$jwt,
			'token_type' 	=> 'Bearer',
			'expires_in' 	=> self::EXPIRE - 1,
		];
	}

	/**
	 * Valida requisição
	 *
	 * @param bool $tokenTypeIsBasic Define se o tipo de token é "Basic", true; ou "Bearer", false.
	 *
	 * @return Action|string Retorna Action quando houver um erro de validação
	 */
	protected function validate($tokenTypeIsBasic = true) {
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

		if ($tokenTypeIsBasic && strtolower($token_type) !== 'basic') {
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

		if (!$tokenTypeIsBasic && strtolower($token_type) !== 'bearer') {
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
