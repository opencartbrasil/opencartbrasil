<?php
class ControllerOrderList extends Controller {
	private const HTTP_NOT_FOUND = 404;

	public function index() {
		$this->load->model('customer/custom_field');
		$this->load->model('sale/order');

		// Filter Status
		if (isset($this->request->get['filter_status'])) {
			$filter_status = !!$this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		// Filter Date Added
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		$date = DateTime::createFromFormat('Y-m-d', $filter_date_added);

		if (!$date || $date->format('Y-m-d') != $filter_date_added) {
			$filter_date_added = null;
		}

		// Filter Date Modified
		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		$date = DateTime::createFromFormat('Y-m-d', $filter_date_modified);

		if (!$date || $date->format('Y-m-d') != $filter_date_modified) {
			$filter_date_modified = null;
		}

		// Page
		if (isset($this->request->get['page'])) {
			$page = max($this->request->get['page'], 1);
		} else {
			$page = 1;
		}

		// Items per page
		if (isset($this->request->get['per_page'])) {
			$per_page = min($this->config->get('db_list_per_page'), $this->request->get['per_page']);
		} else {
			$per_page = $this->config->get('db_list_per_page');
		}

		$filter_data = array(
			'filter_order_status' => $filter_status,
			'filter_date_added' => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		);

		$orders = $this->model_sale_order->getOrders($filter_data);

		if (empty($orders)) {
			return $this->response(array(), self::HTTP_NOT_FOUND);
		}

		$order_total_count = $this->model_sale_order->getTotalOrders($filter_data);

		$custom_fields_codes = $this->model_customer_custom_field->getCustomFieldsCodes();

		$result_items = array();

		foreach ($orders as $key => $order_info) {
			$order_id = intval($order_info['order_id']);

			// Custom Fields
			$custom_fields = array();

			$order_custom_fields = json_decode($order_info['custom_field'], true);

			if (is_array($order_custom_fields)) {
				foreach ($order_custom_fields as $key => $custom_field) {
					$custom_field_key = isset($custom_fields_codes[$key])
						? $custom_fields_codes[$key]
						: $key;

					$custom_fields[$custom_field_key] = $custom_field;
				}
			}

			// Payment Custom Fields
			$payment_custom_fields = array();

			$order_payment_custom_fields = json_decode($order_info['payment_custom_field'], true);

			if (is_array($order_payment_custom_fields)) {
				foreach ($order_payment_custom_fields as $key => $custom_field) {
					$custom_field_key = isset($payment_custom_fields_codes[$key])
						? $payment_custom_fields_codes[$key]
						: $key;

					$payment_custom_fields[$custom_field_key] = $custom_field;
				}
			}

			// Shipping Custom Fields
			$shipping_custom_fields = array();

			$order_shipping_custom_fields = json_decode($order_info['shipping_custom_field'], true);

			if (is_array($order_shipping_custom_fields)) {
				foreach ($order_shipping_custom_fields as $key => $custom_field) {
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
					'options' => $options
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

			$statuses = array_map(function($item) {
				return array(
					'order_status_id' => (int)$item['order_status_id'],
					'date_added' => date('Y-m-d\TH:i:s\+00:00', strtotime($item['date_added'])),
					'comment' => $item['comment'],
					'notify' => !!$item['notify']
				);
			}, $statuses);

			$result_items[] = array(
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
				'products' => $order_products,
				'totals' => $totals,
				'statuses' => $statuses
			);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($order_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));

		// URL Page
		$links = '/api/orders?page=%d&per_page=%d';

		if ($filter_status !== null) {
			$links .= '&filter_status=' . $filter_status;
		}

		if ($filter_date_added !== null) {
			$links .= '&filter_date_added=' . $filter_date_added;
		}

		if ($filter_date_modified !== null) {
			$links .= '&filter_date_modified=' . $filter_date_modified;
		}

		$result = array(
			'records' => $result_items,
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($order_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $order_total_count");
		$this->response($result);
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
