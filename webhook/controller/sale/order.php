<?php
class ControllerWebhookSaleOrder extends Controller {
	public function add($router, $args, $order_id) {
		$this->load->modelWebhook('advanced/webhook');

		$hooks = $this->model_webhook_advanced_webhook->getHooks('order_add');

		$data = array(
			'order_id' => (int)$order_id,
			'action' => 'order_add',
		);

		$this->dispatchRequests('order_add', $hooks, $data);
	}

	public function edit($router, $args) {
		$order_id = $args[0];

		$this->load->modelWebhook('advanced/webhook');

		$hooks = $this->model_webhook_advanced_webhook->getHooks('order_edit');

		$data = array(
			'order_id' => (int)$order_id,
			'action' => 'order_edit',
		);

		$this->dispatchRequests('order_edit', $hooks, $data);
	}

	public function addOrderHistory($router, $args) {
		$order_id = $args[0];

		$this->load->modelWebhook('advanced/webhook');

		$hooks = $this->model_webhook_advanced_webhook->getHooks('order_edit');

		$data = array(
			'order_id' => (int)$order_id,
			'action' => 'order_history_edit',
		);

		$this->dispatchRequests('order_history_edit', $hooks, $data);
	}

	private function dispatchRequests($action, $hooks, $data) {
		ob_start();

		$multiCurl = array();

		$mh = curl_multi_init();

		foreach ($hooks as $hook) {
			$key = $hook['webhook_client_id'];

			$multiCurl[$key] = curl_init($hook['url']);
			curl_setopt($multiCurl[$key], CURLOPT_RETURNTRANSFER, true);
			curl_setopt($multiCurl[$key], CURLOPT_FAILONERROR, false);
			curl_setopt($multiCurl[$key], CURLINFO_HEADER_OUT, true);
			curl_setopt($multiCurl[$key], CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($multiCurl[$key], CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($multiCurl[$key], CURLOPT_HTTPHEADER, $hook['headers']);

			if (mb_strlen(trim($hook['auth'])) > 0) {
				curl_setopt($multiCurl[$key], CURLOPT_USERPWD, $hook['auth']);
			}

			curl_multi_add_handle($mh, $multiCurl[$key]);
		}

		$index = 0;

		do {
			curl_multi_exec($mh,$index);
		} while($index > 0);

		foreach($multiCurl as $webhook_client_id => $ch) {
			$content = curl_multi_getcontent($ch);
			$headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
			$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			$response = $headers . $content;

			$this->model_webhook_advanced_webhook->saveRequest($webhook_client_id, $action, $data, $response, $status_code);

			curl_multi_remove_handle($mh, $ch);
		}

		curl_multi_close($mh);

		ob_end_clean();
	}
}
