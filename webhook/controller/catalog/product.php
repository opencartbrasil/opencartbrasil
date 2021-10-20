<?php
class ControllerWebhookCatalogProduct extends Controller {
	public function add($router, $args, $product_id) {
		$this->load->modelWebhook('advanced/webhook');

		$hooks = $this->model_webhook_advanced_webhook->getHooks('product_add');

		$data = array(
			'product_id' => $product_id,
			'action' => 'product_add',
		);

		$this->dispatchRequests('product_add', $hooks, $data);
	}

	public function copy($router, $args, $output) {
		$this->load->modelWebhook('advanced/webhook');

		$hooks = $this->model_webhook_advanced_webhook->getHooks('product_add');

		$data = array(
			'product_id' => $output,
			'action' => 'product_add',
		);

		$this->dispatchRequests('product_add', $hooks, $data);
	}

	public function edit($router, $args) {
		$product_id = $args[0];

		$this->load->modelWebhook('advanced/webhook');

		$hooks = $this->model_webhook_advanced_webhook->getHooks('product_edit');

		$data = array(
			'product_id' => $product_id,
			'action' => 'product_edit',
		);

		$this->dispatchRequests('product_edit', $hooks, $data);
	}

	public function delete($router, $args) {
		$product_id = $args[0];

		$this->load->modelWebhook('advanced/webhook');

		$hooks = $this->model_webhook_advanced_webhook->getHooks('product_delete');

		$data = array(
			'product_id' => $product_id,
			'action' => 'product_delete',
		);

		$this->dispatchRequests('product_delete', $hooks, $data);
	}

	public function order($router, $args) {
		$order_id = $args[0];
		$order_status_id = $args[1];

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if (!$order_info) {
			return false;
		}

		$order_products = $this->model_checkout_order->getOrderProducts($order_id);

		if (!$order_products) {
			return false;
		}

		$this->load->modelWebhook('advanced/webhook');

		$hooks = $this->model_webhook_advanced_webhook->getHooks('product_edit');

		if (!in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
			foreach ($order_products as $order_product) {
				$data = array(
					'product_id' => $order_product['product_id'],
					'action' => 'product_stock_edit',
				);

				if ($order_product['subtract']) {
					$this->dispatchRequests('product_stock_edit', $hooks, $data);
				}
			}
		}
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
