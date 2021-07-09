<?php

class ControllerCategoryList extends Controller {

	private const HTTP_STATUS_400 = 400;

	public function index() {
		$this->load->model('catalog/category');

		$filter_data = array();

		/**
		 * Filter Parent ID
		 */
		if (isset($this->request->get['filter_parent_id'])) {
			$filter_parent_id = intval($this->request->get['filter_parent_id']);
			$filter_data['parent_id'] = max($filter_parent_id, 0);
		}

		/**
		 * Filter Total Product is equals than ...
		 */
		if (isset($this->request->get['filter_total_products_eq'])) {
			$total_products_eq = intval($this->request->get['filter_total_products_eq']);
			$filter_data['total_products_eq'] = max($total_products_eq, 0);
		}

		/**
		 * Filter Total Product is less than ...
		 */
		if (isset($this->request->get['filter_total_products_lt'])) {
			$filter_total_products_lt = intval($this->request->get['filter_total_products_lt']);
			$filter_data['total_products_lt'] = max($filter_total_products_lt, 1);
		}

		/**
		 * Filter Total Product is greater than ...
		 */
		if (isset($this->request->get['filter_total_products_gt'])) {
			$filter_total_products_gt = intval($this->request->get['filter_total_products_gt']);
			$filter_data['total_products_gt'] = max($filter_total_products_gt, 1);
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

		$filter_data = array_merge(
			$filter_data,
			array(
				'offset' => ($page - 1) * $per_page,
				'limit' => $per_page
			)
		);

		$categories = $this->model_catalog_category->getCategories($filter_data);

		$categories_total_count = $this->model_catalog_category->getTotalCategories($filter_data);

		$result_items = array();

		foreach ($categories as $key => $category_info) {
			$category_id = intval($category_info['category_id']);

			$category_descriptions = $this->model_catalog_category->getCategoryDescriptionById($category_id);

			$item = array(
				'category_id' => $category_id,
				'image' => empty($category_info['image']) ? null : HTTPS_SERVER . 'image/' . $category_info['image'],
				'parent_id' => intval($category_info['parent_id']),
				'top' => intval($category_info['top']),
				'column' => intval($category_info['column']),
				'sort_order' => intval($category_info['sort_order']),
				'status' => !!$category_info['status'],
				'date_added' => date('Y-m-d\TH:i:s\+00:00', strtotime($category_info['date_added'])),
				'date_modified' => date('Y-m-d\TH:i:s\+00:00', strtotime($category_info['date_modified'])),
				'total_products' => intval($category_info['total_products']),
			);

			$result_items[] = array_merge($item, $category_descriptions);
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($categories_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));
		$links = '/category?page=%d&per_page=%d';

		$result = array(
			'items' => array_values($result_items),
			'_metadata' => array(
				'page' => $page,
				'per_page' => $per_page,
				'page_count' => count($result_items),
				'total_count' => $categories_total_count,
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page !== $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $categories_total_count");
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
