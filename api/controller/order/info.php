<?php
class ControllerOrderInfo extends Controller {
	private const HTTP_STATUS_404 = 404;

	public function index(int $order_id = 0) {
		$this->load->model('customer/custom_field');
		$this->load->model('sale/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = intval($this->request->get['order_id']);
		}

		$order_info = $this->model_sale_order->getOrder($order_id);

		if (empty($order_info)) {
			return $this->response([], self::HTTP_STATUS_404);
		}

		// Custom Fields
		$custom_fields_codes = $this->model_customer_custom_field->getCustomFieldsCodes();

		$custom_fields = array();

		if (is_array($order_info['custom_field'])) {
			foreach ($order_info['custom_field'] as $key => $custom_field) {
				$custom_field_key = isset($custom_fields_codes[$key])
					? $custom_fields_codes[$key]
					: $key;

				$custom_fields[$custom_field_key] = $custom_field;
			}
		}

		// Payment Custom Fields
		$payment_custom_fields = array();

		if (is_array($order_info['payment_custom_field'])) {
			foreach ($order_info['payment_custom_field'] as $key => $custom_field) {
				$custom_field_key = isset($payment_custom_fields_codes[$key])
					? $payment_custom_fields_codes[$key]
					: $key;

				$payment_custom_fields[$custom_field_key] = $custom_field;
			}
		}

		// Shipping Custom Fields
		$shipping_custom_fields = array();

		if (is_array($order_info['shipping_custom_field'])) {
			foreach ($order_info['shipping_custom_field'] as $key => $custom_field) {
				$custom_field_key = isset($shipping_custom_fields_codes[$key])
					? $shipping_custom_fields_codes[$key]
					: $key;

				$shipping_custom_fields[$custom_field_key] = $custom_field;
			}
		}

		// Products
		$order_products = $this->model_sale_order->getOrderProducts($order_id);

		$products = array();

		foreach ($order_products as $product) {
			$options = $this->model_sale_order->getOptionsOrderProducts($product['order_product_id']);

			$recurring = $this->model_sale_order->getOrderRecurringByProductId($order_id, $product['product_id']);

			$products[] = array(
				'product_id' => (int)$product['product_id'],
				'name' => $product['name'],
				'model' => $product['model'],
				'quantity' => (int)$product['quantity'],
				'price' => (float)$product['price'],
				'total' => (float)$product['total'],
				'tax' => (float)$product['tax'],
				'reward' => $product['reward'],
				'sku' => $product['sku'],
				'ncm' => $product['ncm'],
				'cest' => $product['cest'],
				'options' => $options,
				'recurring' => $recurring
			);
		}

		// Totals
		$order_totals = $this->model_sale_order->getOrderTotals($order_id);

		$totals = array();

		foreach ($order_totals as $total) {
			$totals[] = array(
				'code' => $total['code'],
				'title' => $total['title'],
				'value' => (float)$total['value'],
				'sort_order' => (int)$total['sort_order']
			);
		}

		// Statuses
		$statuses = $this->model_sale_order->getOrderHistories($order_id, 0, $this->config->get('db_list_per_page'));

		$order_info = array(
			'order_id' => (int)$order_info['order_id'],
			'invoice_no' => $order_info['invoice_no'],
			'invoice_prefix' => $order_info['invoice_prefix'],
			'store_name' => $order_info['store_name'],
			'store_url' => $order_info['store_url'],
			'customer_id' => (int)$order_info['customer_id'],
			'customer_group_id' => (int)$order_info['customer_group_id'],
			'firstname' => $order_info['firstname'],
			'lastname' => $order_info['lastname'],
			'email' => $order_info['email'],
			'telephone' => $order_info['telephone'],
			'fax' => $order_info['fax'],
			'custom_fields' => $custom_fields,
			'payment' => array(
				'firstname' => $order_info['payment_firstname'],
				'lastname' => $order_info['payment_lastname'],
				'company' => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city' => $order_info['payment_city'],
				'postcode' => $order_info['payment_postcode'],
				'country' => $order_info['payment_country'],
				'zone' => $order_info['payment_zone'],
				'custom_field' => $payment_custom_fields,
				'method' => $order_info['payment_method']
			),
			'shipping' => array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname' => $order_info['shipping_lastname'],
				'company' => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city' => $order_info['shipping_city'],
				'postcode' => $order_info['shipping_postcode'],
				'country' => $order_info['shipping_country'],
				'zone' => $order_info['shipping_zone'],
				'custom_field' => $shipping_custom_fields,
				'method' => $order_info['shipping_method']
			),
			'comment' => $order_info['comment'],
			'total' => (float)$order_info['total'],
			'order_status_id' => (int)$order_info['order_status_id'],
			'affiliate_id' => (int)$order_info['affiliate_id'],
			'commission' => (float)$order_info['commission'],
			'marketing_id' => (int)$order_info['marketing_id'],
			'tracking' => $order_info['tracking'],
			'currency_code' => $order_info['currency_code'],
			'currency_value' => (float)$order_info['currency_value'],
			'ip' => $order_info['ip'],
			'forwarded_ip' => $order_info['forwarded_ip'],
			'user_agent' => $order_info['user_agent'],
			'accept_language' => $order_info['accept_language'],
			'date_added' => date('Y-m-d\TH:i:s\+00:00', strtotime($order_info['date_added'])),
			'date_modified' => date('Y-m-d\TH:i:s\+00:00', strtotime($order_info['date_modified'])),
			'products' => $products,
			'totals' => $totals,
			'statuses' => $statuses
		);

		return $this->response(array(
			'result' => true,
			'data' => $order_info
		));
	}

	/**
	 * Display response
	 *
	 * @param int $status
	 *
	 * @return void
	 */
	protected function response(array $data = array(), int $status = 200) {
		$this->response->addHeader('HTTP/1.1 ' . $status);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
}
