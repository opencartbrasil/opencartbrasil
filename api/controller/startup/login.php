<?php

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class ControllerStartupLogin extends Controller {

	public function index() {
		if (in_array($this->request->get['route'], $this->config->get('ignored_routers'))) {
			return;
		}

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

		$this->load->model('credentials/token');

		$is_valid = $this->model_credentials_token->isValid($authorization);

		@list($token_type, $access_token) = explode(' ', $authorization);

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

		if (empty($access_token)) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'forgotten_access_token',
						'message' => 'It is necessary to inform the "access_token".'
					)
				)
			)));
			return new Action('status_code/bad_request');
		}

		try {
			$jwt_decoded = JWT::decode(
				$access_token,
				$this->config->get('secret_key'),
				array('HS256')
			);

			$this->registry->set('jwt', $jwt_decoded);
		} catch (ExpiredException $ignored) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'expired_access_token',
						'message' => 'The access_token is expired.'
					)
				)
			)));
			return new Action('status_code/unauthorized');
		} catch (SignatureInvalidException $ignored) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_access_token',
						'message' => 'Invalid access_token.'
					)
				)
			)));
			return new Action('status_code/unauthorized');
		} catch (UnexpectedValueException $ignored) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_access_token',
						'message' => 'Invalid access_token.'
					)
				)
			)));
			return new Action('status_code/bad_request');
		}
	}
}
