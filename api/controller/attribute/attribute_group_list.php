<?php
class ControllerAttributeAttributeGroupList extends Controller {
	public function index() {
		$this->load->model('catalog/attribute_group');

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

		$attribute_groups = $this->model_catalog_attribute_group->getAttributeGroups($filter_data);

		$attribute_group_total_count = $this->model_catalog_attribute_group->getTotalAttributeGroups();

		$result_items = array();

		foreach ($attribute_groups as $key => $attribute_group) {
			$attribute_group_description = $this->model_catalog_attribute_group->getAttributeGroupDescriptions($attribute_group['attribute_group_id']);

			$result_items[] = array(
				'attribute_group_id' => $attribute_group['attribute_group_id'],
				'sort_order' => $attribute_group['sort_order'],
				'name' => $attribute_group_description
			);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($attribute_group_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));
		$links = '/api/attribute_group?page=%d&per_page=%d';

		$result = array(
			'records' => array_values($result_items),
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($attribute_group_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $attribute_group_total_count");
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
