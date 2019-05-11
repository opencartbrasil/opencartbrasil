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

					$ip = '';

					if (isset($this->request->server['REMOTE_ADDR']) && filter_var($this->request->server['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
						$ip = $this->request->server['REMOTE_ADDR'];
					}

					if (isset($this->request->server['HTTP_X_FORWARDED_FOR'])) {
						$xip = trim(current(explode(',', $this->request->server['HTTP_X_FORWARDED_FOR'])));

						if (filter_var($xip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
							if (isset($this->request->server['SERVER_ADDR']) && $this->request->server['SERVER_ADDR'] != $xip) {
								$ip = $xip;
							}
						}
					}

					if(isset($this->request->server['HTTP_CF_CONNECTING_IP']) && filter_var($this->request->server['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)){
						$ip = $this->request->server['HTTP_CF_CONNECTING_IP'];
					}

					if(isset($this->request->server['HTTP_INCAP_CLIENT_IP']) && filter_var($this->request->server['HTTP_INCAP_CLIENT_IP'], FILTER_VALIDATE_IP)){
						$ip = $this->request->server['HTTP_INCAP_CLIENT_IP'];
					}

					if(isset($this->request->server['HTTP_X_SUCURI_CLIENTIP']) && filter_var($this->request->server['HTTP_X_SUCURI_CLIENTIP'], FILTER_VALIDATE_IP)){
						$ip = $this->request->server['HTTP_X_SUCURI_CLIENTIP'];
					}

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
