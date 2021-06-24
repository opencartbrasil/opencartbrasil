<?php

class ModelCatalogProduct extends Model {
	public function create($product) {
		// Reset values
		if (!isset($product->dimentions)) {
			$product->dimentions = new \stdClass;
			$product->dimentions->weight = 0;
			$product->dimentions->weight_class_id = $this->config->get('config_weight_class_id');
			$product->dimentions->length = 0;
			$product->dimentions->width = 0;
			$product->dimentions->height = 0;
			$product->dimentions->length_class_id = $this->config->get('config_length_class_id');
		}

		/** Register Product */
		$this->db->query('
			INSERT INTO `' . DB_PREFIX . 'product`
			SET `model` = "' . $this->db->escape($product->model) . '",
				`sku` = "' . $this->db->escape($product->sku) . '",
				`ncm` = "' . $this->db->escape($product->ncm) . '",
				`cest` = "' . $this->db->escape($product->cest) . '",
				`upc` = "' . $this->db->escape($product->upc) . '",
				`ean` = "' . $this->db->escape($product->ean) . '",
				`jan` = "' . $this->db->escape($product->jan) . '",
				`isbn` = "' . $this->db->escape($product->isbn) . '",
				`mpn` = "' . $this->db->escape($product->mpn) . '",
				`location` = "' . $this->db->escape($product->location) . '",
				`quantity` = "' . intval($product->quantity) . '",
				`stock_status_id` = "' . intval($product->stock_status_id) . '",
				`image` = "' . $this->db->escape($product->image) . '",
				`manufacturer_id` = "' . intval($product->manufacturer_id) . '",
				`shipping` = "' . !!$product->shipping . '",
				`price` = "' . floatval($product->price) . '",
				`points` = "' . intval($product->points_to_buy) . '",
				`tax_class_id` = "' . intval($product->tax_class_id) . '",
				`date_available` = "' . $this->db->escape($product->date_available) . '",
				`weight` = "' . floatval($product->dimentions->weight) . '",
				`weight_class_id` = "' . intval($product->dimentions->weight_class_id) . '",
				`length` = "' . floatval($product->dimentions->length) . '",
				`width` = "' . floatval($product->dimentions->width) . '",
				`height` = "' . floatval($product->dimentions->height) . '",
				`length_class_id` = "' . intval($product->dimentions->length_class_id) . '",
				`subtract` = "' . !!$product->subtract . '",
				`minimum` = "' . intval($product->minimum) . '",
				`sort_order` = "' . intval($product->sort_order) . '",
				`status` = "' . !!$product->status . '",
				`viewed` = "' . intval($product->viewed) . '",
				`date_added` = NOW(),
				`date_modified` = NOW();
		');

		$product_id = intval($this->db->getLastId());

		/** Register Product Description */
		foreach ($this->config->get('languages') as $language_code => $language) {
			if (isset($product->name[$language_code])) {
				$product_name = $product->name[$language_code];
			} else {
				$product_name = $product->name["default"];
			}

			if (isset($product->description[$language_code])) {
				$product_description = $product->description[$language_code];
			} elseif (isset($product->description["default"])) {
				$product_description = $product->description["default"];
			} else {
				$product_description = '';
			}

			if (isset($product->tags[$language_code])) {
				$product_tags = implode(',', $product->tags[$language_code]);
			} elseif (isset($product->tags["default"])) {
				$product_tags = implode(',', $product->tags["default"]);
			} else {
				$product_tags = "";
			}

			if (isset($product->meta_title[$language_code])) {
				$product_meta_title = $product->meta_title[$language_code];
			} else {
				$product_meta_title = $product->meta_title["default"];
			}

			if (isset($product->meta_keyword[$language_code])) {
				$product_meta_keyword = $product->meta_keyword[$language_code];
			} elseif (isset($product->meta_keyword["default"])) {
				$product_meta_keyword = $product->meta_keyword["default"];
			} else {
				$product_meta_keyword = "";
			}

			$this->db->query('
				INSERT INTO `' . DB_PREFIX . 'product_description`
				SET `product_id` = "' . intval($product_id) . '",
					`language_id` = "' . intval($language['language_id']) . '",
					`name` = "' . $product_name . '",
					`description` = "' . $product_description . '",
					`tag` = "' . $product_tags . '",
					`meta_title` = "' . $product_meta_title . '",
					`meta_description` = "",
					`meta_keyword` = "' . $product_meta_keyword . '"
			');
		}

		/** Register in Stores */
		foreach ($product->stores as $store_id) {
			$this->db->query('
				INSERT INTO `' . DB_PREFIX . 'product_to_store`
				SET `product_id` = "' . intval($product_id) . '",
					`store_id` = "' . intval($store_id) . '"
			');
		}

		/** Register Attributes */
		if (isset($product->attributes)) {
			foreach ($product->attributes as $attribute) {
				foreach ($this->config->get('languages') as $language_code => $language) {
					if (isset($attribute[$language_code])) {
						$attribute_text = $attribute[$language_code];
					} elseif (isset($attribute["default"])) {
						$attribute_text = $attribute["default"];
					} else {
						$attribute_text = '';
					}

					$this->db->query('
						INSERT INTO `' . DB_PREFIX . 'product_attribute`
						SET `product_id` = "' . $product_id . '",
							`attribute_id` = "' . intval($attribute->id) . '",
							`language_id` = "' . intval($language['language_id']) . '",
							`text` = "' . $this->db->escape($attribute_text) . '"
					');
				}
			}
		}

		/** Register Discounts */
		if (isset($product->discounts)) {
			foreach ($product->discount as $discount) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_discount`
					SET `product_id` = "' . $product_id . '",
						`customer_group_id` = "' . intval($discount->customer_group_id) . '",
						`quantity` = "' . intval(isset($discount->quantity) ? $discount->quantity : 0) . '",
						`priority` = "' . intval(isset($discount->priority) ? $discount->priority : 1) . '",
						`price` = "' . floatval($discount->price) . '",
						`date_start` = "' . $this->db->escape($discount->date_start) . '",
						`date_end` = "' . $this->db->escape($discount->date_end) . '"
				');
			}
		}

		/** Register Filters */
		if (isset($product->filters)) {
			foreach ($product->filters as $filter_id) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_filter`
					SET `product_id` = "' . $product_id . '",
						`filter_id` = "' . intval($filter_id) . '"
				');
			}
		}

		/** Register Additional Images */
		if (isset($product->additional_images)) {
			foreach ($product->additional_images as $key => $image) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_image`
					SET `product_id` = "' . $product_id . '",
						`image` = "' . $this->db->escape($image) . '",
						`sort_order` = "' . intval($key) . '"
				');
			}
		}

		$product->id = $product_id;

		return $product;
	}

	/**
	 * Verifica se um ou mais produto existe, através do ID
	 *
	 * @param int[] $data
	 *
	 * @return bool|array Retorna true/false ou um array com as diferenças de IDs
	 */
	public function getHasProductById(array $data = array()) {
		$products_id = array_map('intval', $data);

		$result = $this->db->query('SELECT product_id FROM `' . DB_PREFIX . 'product` WHERE `product_id` IN (' . implode(',', $products_id) . ')');

		if (count($products_id) > $result->num_rows) {
			$products_founds = array_map(function($item) {
				return $item['product_id'];
			}, $result->rows);

			return array_diff($products_id, $products_founds);
		}

		return true;
	}
}
