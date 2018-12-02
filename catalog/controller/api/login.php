<?php
class ControllerApiLogin extends Controller {
	public function index() {
		$this->load->language('api/login');

		$json = array();

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

			if (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)){
				$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
			}

			if(isset($_SERVER['HTTP_INCAP_CLIENT_IP']) && filter_var($_SERVER['HTTP_INCAP_CLIENT_IP'], FILTER_VALIDATE_IP)){
				$ip = $_SERVER['HTTP_INCAP_CLIENT_IP'];
			}

			if(isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']) && filter_var($_SERVER['HTTP_X_SUCURI_CLIENTIP'], FILTER_VALIDATE_IP)){
				$ip = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
			}

			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$xip = trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));

				if (filter_var($xip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
					$ip = $xip;
				}
			}

			if (!in_array($ip, $ip_data)) {
				$json['error']['ip'] = sprintf($this->language->get('error_ip'), $ip);
			}

			if (!$json) {
				$json['success'] = $this->language->get('text_success');

				$session = new Session($this->config->get('session_engine'), $this->registry);
				$session->start();

				$this->model_account_api->addApiSession($api_info['api_id'], $session->getId(), $ip);

				$session->data['api_id'] = $api_info['api_id'];

				// Create Token
				$json['api_token'] = $session->getId();
			} else {
				$json['error']['key'] = $this->language->get('error_key');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
