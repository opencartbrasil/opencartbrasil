<?php
class ControllerOptionList extends Controller {
	public function index() {
		$this->load->model('catalog/option');

		// Filter Name
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		// Filter Type
		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = null;
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
			'filter_name' => $filter_name,
			'filter_type' => $filter_type,
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		);

		$options = $this->model_catalog_option->getOptions($filter_data);

		$options_total_count = $this->model_catalog_option->getTotalOptions($filter_data);

		$result_items = array();

		foreach ($options as $key => $option) {
			$option_id = intval($option['option_id']);

			$option_descriptions = $this->model_catalog_option->getOptionDescriptions($option_id);

			$option_values = $this->model_catalog_option->getOptionValues($option_id);

			foreach ($option_values as &$value) {
				$value_descriptions = $this->model_catalog_option->getOptionValueDescriptions($value['option_value_id']);

				$value = array(
					'option_value_id' => intval($value['option_value_id']),
					'image' => !empty($value['image']) ? HTTPS_CATALOG . $value['image'] : null,
					'sort_order' => intval($value['sort_order']),
					'name' => $value_descriptions
				);
			}

			$option_info = array(
				'option_id' => intval($option['option_id']),
				'type' => $option['type'],
				'sort_order' => intval($option['sort_order']),
				'name' => $option_descriptions,
				'values' => $option_values
			);

			$result_items[] = $option_info;
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($options_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));

		// URL Page
		$links = '/api/option?page=%d&per_page=%d';

		if ($filter_name !== null) {
			$links .= '&filter_name=' . urlencode($filter_name);
		}

		if ($filter_type !== null) {
			$links .= '&filter_type=' . urlencode($filter_type);
		}

		$result = array(
			'records' => array_values($result_items),
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($options_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $options_total_count");
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
