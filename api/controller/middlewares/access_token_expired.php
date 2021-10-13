<?php
class ControllerMiddlewaresAccessTokenExpired extends Controller {
	public function before() {
		if (in_array($this->request->get['route'], $this->config->get('ignored_routers'))) {
			return;
		}

		if (isset($this->request->headers['authorization'])) {
			$authorization = $this->request->headers['authorization'];
		} else {
			$authorization = '';
		}

		@list($token_type, $access_token) = explode(' ', $authorization);

		$this->load->model('credentials/token');

		$isValid = $this->model_credentials_token->accessTokenIsValid($access_token);

		if (!$isValid) {
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
		}
	}
}
