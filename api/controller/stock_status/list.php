<?php
class ControllerStockStatusList extends Controller {
	public function index() {
		$this->load->model('localisation/stock_status');

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
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		);

		$stock_status = $this->model_localisation_stock_status->getStockStatuses($filter_data);

		$stock_status_total_count = $this->model_localisation_stock_status->getTotalStockStatuses();

		$result_items = array();

		foreach ($stock_status as $key => $status) {
			$stock_status_id = $status['stock_status_id'];
			$language_code = $status['language_code'];

			$result_items[$stock_status_id]['stock_status_id'] = $stock_status_id;
			$result_items[$stock_status_id]['name'][$language_code] = $status['name'];
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($stock_status_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));
		$links = '/api/stock_status?page=%d&per_page=%d';

		$result = array(
			'records' => array_values($result_items),
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($stock_status_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $stock_status_total_count");
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
