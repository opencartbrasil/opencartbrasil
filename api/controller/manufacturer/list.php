<?php
class ControllerManufacturerList extends Controller {
	public function index() {
		$this->load->model('catalog/manufacturer');

		// Filter Name
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
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

		$filter_data = [
			'filter_name' => $filter_name,
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		];

		$manufacturers = $this->model_catalog_manufacturer->getManufacturers($filter_data);

		$manufacturers_total_count = $this->model_catalog_manufacturer->getTotalManufacturers($filter_data);

		$result_items = array();

		foreach ($manufacturers as $key => $manufacturer_info) {
			$manufacturer_id = intval($manufacturer_info['manufacturer_id']);

			$result_items[] = array(
				'manufacturer_id' => intval($manufacturer_id),
				'name' => $manufacturer_info['name'],
				'image' => !empty($manufacturer_info['image']) ? HTTPS_CATALOG . 'image/' . $manufacturer_info['image'] : null,
				'sort_order' => intval($manufacturer_info['sort_order']),
			);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($manufacturers_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));

		// URL Page
		$links = '/api/manufacturer?page=%d&per_page=%d';

		if ($filter_name !== null) {
			$links .= '&filter_name=' . urlencode($filter_name);
		}

		$result = array(
			'records' => array_values($result_items),
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($manufacturers_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $manufacturers_total_count");
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
