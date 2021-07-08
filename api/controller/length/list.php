<?php

class ControllerLengthList extends Controller {

	private const HTTP_STATUS_400 = 400;

	public function index() {
		$this->load->model('localisation/length_class');

		/**
		 * Filter Unit
		 */
		if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = null;
		}

		/**
		 * Page
		 */
		if (isset($this->request->get['page'])) {
			$page = intval($this->request->get['page']);
			$page = max($page, 1);
		} else {
			$page = 1;
		}

		/**
		 * Items per page
		 */
		if (isset($this->request->get['per_page'])) {
			$per_page = intval($this->request->get['per_page']);
			$per_page = min($this->config->get('db_list_per_page'), $per_page);
		} else {
			$per_page = $this->config->get('db_list_per_page');
		}

		$filter_data = array(
			'unit' => $filter_unit,
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		);

		$lengths = $this->model_localisation_length_class->getLengthClasses($filter_data);

		$length_total_count = $this->model_localisation_length_class->getTotalLengthClasses();

		$result_items = array();

		foreach ($lengths as $key => $length_info) {
			$length_class_id = intval($length_info['length_class_id']);

			if (!isset($result_items[$length_class_id]['descriptions'])) {
				$result_items[$length_class_id]['descriptions'] = array();
			}

			$result_items[$length_class_id] = array(
				'length_class_id' => $length_class_id,
				'value' => floatval($length_info['value']),
				'unit' => $length_info['unit'],
				'descriptions' => array_merge(
					$result_items[$length_class_id]['descriptions'],
					array($length_info['language_code'] => $length_info['title'])
				)
			);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($length_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));
		$links = '/length?page=%d&per_page=%d';

		$result = array(
			'items' => array_values($result_items),
			'_metadata' => array(
				'page' => $page,
				'per_page' => $per_page,
				'page_count' => count($result_items),
				'total_count' => $length_total_count,
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page !== $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $length_total_count");
		$this->response($result);
	}

	/**
	 * Exibe resposta para o cliente
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
