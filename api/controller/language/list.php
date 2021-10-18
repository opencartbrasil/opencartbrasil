<?php
class ControllerLanguageList extends Controller {
	public function index() {
		$this->load->model('localisation/language');

		// Filter Code
		if (isset($this->request->get['filter_code'])) {
			$filter_code = $this->request->get['filter_code'];
		} else {
			$filter_code = null;
		}

		// Filter Status
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
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
			'filter_code' => $filter_code,
			'filter_status' => $filter_status,
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		);

		$languages = $this->model_localisation_language->getLanguages($filter_data);

		$language_total_count = $this->model_localisation_language->getTotalLanguages($filter_data);

		$result_items = array();

		foreach ($languages as $key => $language_info) {
			$result_items[] = array(
				'language_id' => intval($language_info['language_id']),
				'name' => $language_info['name'],
				'code' => $language_info['code'],
				'locale' => $language_info['locale'],
				'sort_order' => intval($language_info['sort_order']),
				'status' => !!$language_info['status']
			);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($language_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));

		// URL Page
		$links = '/api/languages?page=%d&per_page=%d';

		if ($filter_code !== null) {
			$links .= '&filter_code=' . urlencode($filter_code);
		}

		if ($filter_status !== null) {
			$links .= '&filter_status=' . $filter_status;
		}

		$result = array(
			'records' => $result_items,
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($language_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $language_total_count");
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
