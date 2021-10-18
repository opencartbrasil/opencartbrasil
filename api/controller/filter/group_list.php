<?php
class ControllerFilterGroupList extends Controller {
	public function index() {
		$this->load->model('catalog/filter');

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

		$filters = $this->model_catalog_filter->getFilterGroups($filter_data);

		$filter_total_count = $this->model_catalog_filter->getTotalFilterGroups();

		$result_items = array();

		foreach ($filters as $key => $filter_info) {
			$filter_description = $this->model_catalog_filter->getFilterGroupDescriptionById($filter_info['filter_group_id']);
			$filters = $this->model_catalog_filter->getFiltersByGroupId($filter_info['filter_group_id']);

			$item = array(
				'filter_group_id' => intval($filter_info['filter_group_id']),
				'sort_order' => intval($filter_info['sort_order']),
				'filters' => array_values($filters)
			);

			$result_items[] = array_merge($item, $filter_description);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($filter_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));
		$links = '/api/filter_group?page=%d&per_page=%d';

		$result = array(
			'records' => array_values($result_items),
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($filter_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $filter_total_count");
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
