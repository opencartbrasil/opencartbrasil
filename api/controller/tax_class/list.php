<?php
class ControllerTaxClassList extends Controller {
	public function index() {
		$this->load->model('localisation/tax_class');

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

		$tax_classes = $this->model_localisation_tax_class->getTaxClasses($filter_data);

		$tax_class_total_count = $this->model_localisation_tax_class->getTotalTaxClasses();

		$result_items = array();

		foreach ($tax_classes as $key => $tax_class_info) {
			$result_items[] = array(
				'tax_class_id' => intval($tax_class_info['tax_class_id']),
				'title' => $tax_class_info['title'],
				'description' => $tax_class_info['description'],
				'date_added' => date('Y-m-d\TH:i:s\+00:00', strtotime($tax_class_info['date_added'])),
				'date_modified' => date('Y-m-d\TH:i:s\+00:00', strtotime($tax_class_info['date_modified'])),
			);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($tax_class_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));
		$links = '/api/tax_class?page=%d&per_page=%d';

		$result = array(
			'records' => $result_items,
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($tax_class_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $tax_class_total_count");
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
