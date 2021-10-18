<?php
class ControllerProductList extends Controller {
	public function index() {
		$this->load->model('catalog/product');

		// Filter Name
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		// Filter Quantity
		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = intval($this->request->get['filter_quantity']);
		} else {
			$filter_quantity = null;
		}

		// Filter Status
		if (isset($this->request->get['filter_status'])) {
			$filter_status = !!$this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		// Filter Date Added
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		$date = DateTime::createFromFormat('Y-m-d', $filter_date_added);

		if (!$date || $date->format('Y-m-d') != $filter_date_added) {
			$filter_date_added = null;
		}

		// Filter Date Modified
		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		$date = DateTime::createFromFormat('Y-m-d', $filter_date_modified);

		if (!$date || $date->format('Y-m-d') != $filter_date_modified) {
			$filter_date_modified = null;
		}

		/// Filter Manufacturer Id
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = intval($this->request->get['filter_manufacturer_id']);
		} else {
			$filter_manufacturer_id = null;
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
			'filte_quantity' => $filter_quantity,
			'filte_status' => $filter_status,
			'filte_date_added' => $filter_date_added,
			'filte_date_modified' => $filter_date_modified,
			'filte_manufacturer_id' => $filter_manufacturer_id,
			'offset' => ($page - 1) * $per_page,
			'limit' => $per_page
		);

		$products = $this->model_catalog_product->getProducts($filter_data);

		$product_total_count = $this->model_catalog_product->getTotalProducts($filter_data);

		$result_items = array();

		foreach ($products as $key => $product_info) {
			$product_id = intval($product_info['product_id']);

			$result_items[$key] = array(
				'product_id' => intval($product_info['product_id']),
				'model' => $product_info['model'],
				'sku' => $product_info['sku'],
				'ncm' => $product_info['ncm'],
				'cest' => $product_info['cest'],
				'upc' => $product_info['upc'],
				'ean' => $product_info['ean'],
				'jan' => $product_info['jan'],
				'isbn' => $product_info['isbn'],
				'mpn' => $product_info['mpn'],
				'location' => $product_info['location'],
				'quantity' => intval($product_info['quantity']),
				'stock_status_id' => intval($product_info['stock_status_id']),
				'image' => HTTPS_CATALOG . 'image/' . $product_info['image'],
				'manufacturer_id' => intval($product_info['manufacturer_id']),
				'shipping' => !!$product_info['shipping'],
				'price' => floatval($product_info['price']),
				'points' => intval($product_info['points']),
				'tax_class_id' => intval($product_info['tax_class_id']),
				'date_available' => $product_info['date_available'],
				'dimensions' => array(
					'length' => floatval($product_info['length']),
					'width' => floatval($product_info['width']),
					'height' => floatval($product_info['height']),
					'weight' => floatval($product_info['weight']),
					'length_class_id' => intval($product_info['length_class_id']),
					'weight_class_id' => intval($product_info['weight_class_id'])
				),
				'subtract' => !!$product_info['subtract'],
				'minimum' => intval($product_info['minimum']),
				'sort_order' => intval($product_info['sort_order']),
				'status' => !!$product_info['status'],
				'viewed' => intval($product_info['viewed']),
				'date_added' => date('Y-m-d\TH:i:s\+00:00', strtotime($product_info['date_added'])),
				'date_modified' => date('Y-m-d\TH:i:s\+00:00', strtotime($product_info['date_modified']))
			);

			// Attributes
			$product_attributes = $this->model_catalog_product->getProductAttributes($product_id);

			if ($product_attributes) {
				$attributes = array();

				foreach ($product_attributes as $key => $attribute) {
					$attributes[$attribute['attribute_id']]['id'] = intval($attribute['attribute_id']);
					$attributes[$attribute['attribute_id']][$attribute['code']] = $attribute['text'];
				}

				$result_items[$key]['attributes'] = array_values($attributes);
			}

			// Descriptions
			$product_descriptions = $this->model_catalog_product->getProductDescriptions($product_id);

			if ($product_descriptions) {
				$result_items[$key] = array_merge($result_items[$key], array_filter($product_descriptions));
			}

			// Discounts
			$product_discounts = $this->model_catalog_product->getProductDiscounts($product_id);

			if ($product_discounts) {
				foreach ($product_discounts as $discount) {
					$result_items[$key]['discounts'][] = array(
						'customer_group_id' => intval($discount['customer_group_id']),
						'priority' => intval($discount['priority']),
						'price' => floatval($discount['price']),
						'quantity' => intval($discount['quantity']),
						'date_start' => $discount['date_start'],
						'date_end' => $discount['date_end']
					);
				}
			}

			// Filters
			$product_filters = $this->model_catalog_product->getProductFilters($product_id);

			if ($product_filters) {
				$result_items[$key]['filters'] = array_map('intval', $product_filters);
			}

			// Additional Images
			$product_images = $this->model_catalog_product->getProductImages($product_id);

			if ($product_images) {
				foreach ($product_images as $image) {
					$result_items[$key]['additional_images'][] = HTTPS_CATALOG . 'image/' . $image['image'];
				}
			}

			// Options
			$options = $this->model_catalog_product->getProductOptions($product_id);

			foreach ($options as $option) {
				$option_value = $this->model_catalog_product->getProductOptionValues($option['product_option_id']);

				$product_option_values = array();

				foreach ($option_value as $value) {
					$product_option_values[] = array(
						'option_value_id' => intval($value['option_value_id']),
						'sku' => $value['sku'],
						'quantity' => intval($value['quantity']),
						'subtract' => !!$value['subtract'],
						'price' => array(
							'prefix' => $value['price_prefix'],
							'value' => floatval($value['price'])
						),
						'points' => array(
							'prefix' => $value['points_prefix'],
							'value' => floatval($value['points'])
						),
						'weight' => array(
							'prefix' => $value['weight_prefix'],
							'value' => floatval($value['weight'])
						),
					);
				}

				$result_items[$key]['options'][] = array_filter(array(
					'type' => $option['type'],
					'option_id' => intval($option['option_id']),
					'required' => !!$option['required'],
					'value' => $option['value'],
					'values' => $product_option_values
				));
			}

			// Recurring
			$product_recurrings = $this->model_catalog_product->getProductRecurrings($product_id);

			if ($product_recurrings) {
				foreach ($product_recurrings as $recurring) {
					$result_items[$key]['recurring'][] = array(
						'recurring_id' => intval($recurring['recurring_id']),
						'customer_group_id' => intval($recurring['customer_group_id'])
					);
				}
			}

			// Products Related
			$products_related = $this->model_catalog_product->getProductsRelated($product_id);

			if ($products_related) {
				foreach ($products_related as $product_related) {
					$result_items[$key]['product_related'][] = intval($product_related['related_id']);
				}
			}

			// Products Reward
			$products_reward = $this->model_catalog_product->getProductsReward($product_id);

			if ($products_reward) {
				foreach ($products_reward as $reward) {
					$result_items[$key]['points_reward'][] = array(
						'customer_group_id' => intval($reward['customer_group_id']),
						'points' => intval($reward['points'])
					);
				}
			}

			// Price Special
			$special = $this->model_catalog_product->getProductSpecial($product_id);

			if ($special) {
				foreach ($special as $value) {
					$result_items[$key]['special'][] = array(
						'customer_group_id' => intval($value['customer_group_id']),
						'price' => floatval($value['price']),
						'priority' => intval($value['priority']),
						'date_start' => $value['date_start'],
						'date_end' => $value['date_end']
					);
				}
			}

			// Discounts
			$discounts = $this->model_catalog_product->getProductDiscounts($product_id);

			if ($discounts) {
				foreach ($discounts as $discount) {
					$result_items[$key]['discounts'][] = array(
						'customer_group_id' => intval($discount['customer_group_id']),
						'price' => floatval($discount['price']),
						'priority' => intval($discount['priority']),
						'quantity' => intval($discount['quantity']),
						'date_start' => $discount['date_start'],
						'date_end' => $discount['date_end']
					);
				}
			}

			// Categories
			$categories = $this->model_catalog_product->getProductCategories($product_id);

			if ($categories) {
				foreach ($categories as $category) {
					$result_items[$key]['categories'][] = intval($category['category_id']);
				}
			}

			// Downloads
			$downloads = $this->model_catalog_product->getProductDownloads($product_id);

			if ($downloads) {
				foreach ($downloads as $download) {
					$result_items[$key]['downloads'][] = intval($download['download_id']);
				}
			}

			// Stores
			$stores = $this->model_catalog_product->getProductStores($product_id);

			if ($stores) {
				foreach ($stores as $store) {
					$result_items[$key]['stores'][] = intval($store['store_id']);
				}
			}

			// Seo URL
			$product_links = array();

			if ($this->config->get('config_seo_url')) {
				$seo_url = $this->model_catalog_product->getProductSeoUrls($product_id);

				if ($seo_url) {
					foreach ($seo_url as $url) {
						$product_links[$url['language_code']] = HTTPS_CATALOG . $url['keyword'];
					}
				}
			}

			$product_links['default'] = HTTPS_CATALOG . 'index.php?route=product/product&product_id=' . intval($product_id);

			$result_items[$key]['links'] = $product_links;
		}

		$prev_page = max(1, $page - 1);
		$last_page = ceil($product_total_count / $per_page);
		$next_page = intval(min($page + 1, $last_page));

		// URL Page
		$links = '/api/product?page=%d&per_page=%d';

		if ($filter_name !== null) {
			$links .= '&filter_name=' . $filter_name;
		}

		if ($filter_quantity !== null) {
			$links .= '&filter_quantity=' . $filter_quantity;
		}

		if ($filter_status !== null) {
			$links .= '&filter_status=' . $filter_status;
		}

		if ($filter_date_added !== null) {
			$links .= '&filter_date_added=' . $filter_date_added;
		}

		if ($filter_date_modified !== null) {
			$links .= '&filter_date_modified=' . $filter_date_modified;
		}

		if ($filter_manufacturer_id !== null) {
			$links .= '&filter_manufacturer_id=' . $filter_manufacturer_id;
		}

		$result = array(
			'records' => $result_items,
			'_metadata' => array(
				'page' => intval($page),
				'per_page' => intval($per_page),
				'page_count' => count($result_items),
				'total_count' => intval($product_total_count),
				'links' => array(
					'self' => sprintf($links, $page, $per_page),
					'first' => sprintf($links, 1, $per_page),
					'previous' => ($page > 1) ? sprintf($links, $prev_page, $per_page) : null,
					'next' => ($next_page != $page) ? sprintf($links, $next_page, $per_page) : null,
					'last' => sprintf($links, $last_page, $per_page)
				)
			)
		);

		$this->response->addHeader("X-Total-Count: $product_total_count");
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
