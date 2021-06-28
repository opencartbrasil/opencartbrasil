<?php

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class ControllerStartupLogin extends Controller {

	public function index() {
		$ignoredRoutes = [
			'credentials/token',
		];

		if (in_array($this->request->get['route'], $ignoredRoutes)) {
			return;
		}

		if (!isset($this->request->headers['authorization'])) {
			return new Action('status_code/unauthorized');
		}

		$authorization = $this->request->headers['authorization'];

		$this->load->model('credentials/token');

		$is_valid = $this->model_credentials_token->isValid($authorization);

		@list($token_type, $access_token) = explode(' ', $authorization);

		if (strtolower($token_type) !== 'bearer') {
			return new Action('status_code/bad_request');
		}

		if (empty($access_token)) {
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
			return new Action('status_code/unauthorized');
		} catch (SignatureInvalidException $ignored) {
			return new Action('status_code/bad_request');
		} catch (UnexpectedValueException $ignored) {
			return new Action('status_code/bad_request');
		}
	}
}
