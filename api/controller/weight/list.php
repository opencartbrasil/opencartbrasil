<?php
class ControllerWeightList extends Controller {
	public function index() {
		$this->load->model('localisation/weight_class');

		// Filter Unit
		if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = null;
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
			'filter_unit' => $filter_unit,
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		);

		$weights = $this->model_localisation_weight_class->getWeightClasses($filter_data);

		$weight_total_count = $this->model_localisation_weight_class->getTotalWeightClasses($filter_data);

		$result_items = array();

		foreach ($weights as $key => $weight_info) {
			$weight_class_id = intval($weight_info['weight_class_id']);

			$titles = $this->model_localisation_weight_class->getWeightClassDescriptions($weight_class_id);

			$weight = array(
				'weight_class_id' => $weight_class_id,
				'value' => floatval($weight_info['value'])
			);

			$result_items[] = array_merge($weight, $titles);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($weight_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));

		// URL Page
		$links = '/api/weight?page=%d&per_page=%d';

		if ($filter_unit !== null) {
			$links .= '&filter_unit=' . urlencode($filter_unit);
		}

		$result = array(
			'records' => array_values($result_items),
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($weight_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $weight_total_count");
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
