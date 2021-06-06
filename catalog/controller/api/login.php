<?php
class ControllerApiLogin extends Controller {
	public function index() {
		$json = array();

		$this->load->language('api/login');

		if (isset($this->request->server['REQUEST_METHOD']) && $this->request->server['REQUEST_METHOD'] === 'POST') {
			if (isset($this->request->post['username']) && isset($this->request->post['key'])) {
				$this->load->model('account/api');

				// Login with API Key
				$api_info = $this->model_account_api->login($this->request->post['username'], $this->request->post['key']);

				if ($api_info) {
					// Check if IP is allowed
					$ip_data = array();

					$results = $this->model_account_api->getApiIps($api_info['api_id']);

					foreach ($results as $result) {
						$ip_data[] = trim($result['ip']);
					}

					$ip = $this->request->server['REMOTE_ADDR'];

					if (in_array($ip, $ip_data)) {
						$session = new Session($this->config->get('session_engine'), $this->registry);
						$session->start();

						$this->model_account_api->addApiSession($api_info['api_id'], $session->getId(), $ip);

						$session->data['api_id'] = $api_info['api_id'];

						// Create Token
						$http_code = '201 Created';

						$json['success'] = $this->language->get('text_success');
						$json['api_token'] = $session->getId();
					} else {
						$http_code = '403 Forbidden';

						$json['error']['ip'] = sprintf($this->language->get('error_ip'), $ip);
					}
				} else {
					$http_code = '401 Unauthorized';

					$json['error']['key'] = $this->language->get('error_key');
				}
			} else {
				$http_code = '400 Bad Request';

				$json['error']['request'] = $this->language->get('error_request');
			}
		} else {
			$http_code = '405 Method Not Allowed';

			$json['error']['method'] = $this->language->get('error_method');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' ' . $http_code);
		$this->response->setOutput(json_encode($json));
	}
}
