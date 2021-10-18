<?php
class ControllerAttributeAttributeList extends Controller {
	public function index() {
		$this->load->model('catalog/attribute');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_attribute_group_id'])) {
			$filter_attribute_group_id = $this->request->get['filter_attribute_group_id'];
		} else {
			$filter_attribute_group_id = null;
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
			'filter_attribute_group_id' => $filter_attribute_group_id,
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		);

		$attributes = $this->model_catalog_attribute->getAttributes($filter_data);

		$attributes_total_count = $this->model_catalog_attribute->getTotalAttributes($filter_data);

		$result_items = array();

		foreach ($attributes as $key => $attribute) {
			$attribute_descriptions = $this->model_catalog_attribute->getAttributeDescriptions($attribute['attribute_id']);

			$result_items[] = array(
				'attribute_id' => $attribute['attribute_id'],
				'attribute_group_id' => $attribute['attribute_group_id'],
				'name' => $attribute_descriptions
			);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($attributes_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));

		// URL Page
		$links = '/api/attribute?page=%d&per_page=%d';

		if (isset($this->request->get['filter_name'])) {
			$links .= '&filter_name=' . urlencode($this->request->get['filter_name']);
		}

		if (isset($this->request->get['filter_attribute_group_id'])) {
			$links .= '&filter_attribute_group_id=' . urlencode($this->request->get['filter_attribute_group_id']);
		}

		$result = array(
			'records' => array_values($result_items),
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($attributes_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $attributes_total_count");
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
