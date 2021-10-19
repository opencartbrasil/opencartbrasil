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
				$this->config->get('api_secret_key'),
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
		} catch (SignatureInvalidException | UnexpectedValueException $ignored) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_access_token',
						'message' => 'Invalid access_token.'
					)
				)
			)));

			if ($ignored instanceof UnexpectedValueException) {
				return new Action('status_code/bad_request');
			}

			return new Action('status_code/unauthorized');
		} catch (DomainException $ignored) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_jwt',
						'message' => 'The token must be in the JWT pattern.'
					)
				)
			)));

			return new Action('status_code/bad_request');
		}
	}
}
