<?php
class ModelCatalogProduct extends Model {
	public function add($product) {
		// Reset values
		if (!isset($product->dimensions)) {
			$product->dimensions = new \stdClass;
			$product->dimensions->weight = 0;
			$product->dimensions->weight_class_id = $this->config->get('config_weight_class_id');
			$product->dimensions->length = 0;
			$product->dimensions->width = 0;
			$product->dimensions->price = 0;
			$product->dimensions->price = 0;
			$product->dimensions->length_class_id = $this->config->get('config_length_class_id');
		}

		// Register Product
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
				`weight` = "' . floatval($product->dimensions->weight) . '",
				`weight_class_id` = "' . intval($product->dimensions->weight_class_id) . '",
				`length` = "' . floatval($product->dimensions->length) . '",
				`width` = "' . floatval($product->dimensions->width) . '",
				`height` = "' . floatval($product->dimensions->height) . '",
				`length_class_id` = "' . intval($product->dimensions->length_class_id) . '",
				`subtract` = "' . !!$product->subtract . '",
				`minimum` = "' . intval($product->minimum) . '",
				`sort_order` = "' . intval($product->sort_order) . '",
				`status` = "' . !!$product->status . '",
				`viewed` = "' . intval($product->viewed) . '",
				`date_added` = NOW(),
				`date_modified` = NOW();
		');

		$product_id = intval($this->db->getLastId());

		$this->saveData($product_id, $product);

		$product->id = $product_id;

		return $product->id;
	}

	public function update(int $product_id, $product) {
		// Reset values
		if (!isset($product->dimensions)) {
			$product->dimensions = new \stdClass;
			$product->dimensions->weight = 0;
			$product->dimensions->weight_class_id = $this->config->get('config_weight_class_id');
			$product->dimensions->length = 0;
			$product->dimensions->width = 0;
			$product->dimensions->height = 0;
			$product->dimensions->length_class_id = $this->config->get('config_length_class_id');
		}

		// Register Product
		$this->db->query('
			UPDATE `' . DB_PREFIX . 'product`
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
				`weight` = "' . floatval($product->dimensions->weight) . '",
				`weight_class_id` = "' . intval($product->dimensions->weight_class_id) . '",
				`length` = "' . floatval($product->dimensions->length) . '",
				`width` = "' . floatval($product->dimensions->width) . '",
				`height` = "' . floatval($product->dimensions->height) . '",
				`length_class_id` = "' . intval($product->dimensions->length_class_id) . '",
				`subtract` = "' . !!$product->subtract . '",
				`minimum` = "' . intval($product->minimum) . '",
				`sort_order` = "' . intval($product->sort_order) . '",
				`status` = "' . !!$product->status . '",
				`viewed` = "' . intval($product->viewed) . '",
				`date_added` = NOW(),
				`date_modified` = NOW()
			WHERE `product_id` = "' . intval($product_id) . '";
		');

		$this->saveData($product_id, $product);

		return $product_id;
	}

	public function updateStock(int $product_id, $product) {
		$this->db->query('
			UPDATE `' . DB_PREFIX . 'product`
			SET `location` = "' . $this->db->escape(isset($product->location) ? $product->location : '') . '",
				`minimum` = "' . (int)$product->minimum . '",
				`quantity` = "' . (int)$product->quantity . '",
				`weight` = "' . (float)$product->weight . '",
				`length` = "' . (float)$product->length . '",
				`width` = "' . (float)$product->width . '",
				`height` = "' . (float)$product->height . '",
				`price` = "' . (float)$product->price . '",
				`weight_class_id` = "' . intval(isset($product->weight_class_id) ? $product->weight_class_id : $this->config->get('config_weight_class_id')) . '",
				`length_class_id` = "' . intval(isset($product->length_class_id) ? $product->length_class_id : $this->config->get('config_length_class_id')) . '"
			WHERE `product_id` = "' . $product_id . '"
		');
	}

	public function getProduct(int $product_id) {
		$query = $this->db->query('
			SELECT
				DISTINCT *,
				CONVERT_TZ(`date_added`, @@time_zone, "+00:00") AS `date_added`,
				CONVERT_TZ(`date_modified`, @@time_zone, "+00:00") AS `date_modified`
			FROM `' . DB_PREFIX . 'product`
			WHERE `product_id` = "' . $product_id . '"
		');

		return $query->row;
	}

	public function getProducts(array $data = array()) {
		$sql = '
			SELECT DISTINCT p.*,
				CONVERT_TZ(p.`date_added`, @@time_zone, "+00:00") AS `date_added`,
				CONVERT_TZ(p.`date_modified`, @@time_zone, "+00:00") AS `date_modified`
			FROM `' . DB_PREFIX . 'product` p
			LEFT JOIN `' . DB_PREFIX . 'product_description` pd ON (pd.product_id = p.product_id)
			WHERE p.product_id > 0
		';

		if (!empty($data['filter_name'])) {
			$sql .= ' AND pd.`name` LIKE "%' . $this->db->escape($data['filter_name']) . '%"';
		}

		if (!empty($data['filter_quantity'])) {
			$sql .= ' AND p.`quantity` <= "' . intval($data['filter_quantity']) . '"';
		}

		if (!empty($data['filter_status'])) {
			$sql .= ' AND p.`status` = "' . !!$data['filter_status'] . '"';
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= ' AND p.`date_added` <= DATE("' . $this->db->escape($data['filter_date_added']) . '")';
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= ' AND p.`date_modified` <= DATE("' . $this->db->escape($data['filter_date_modified']) . '")';
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= ' AND p.`manufacturer_id` = "' . intval($data['filter_manufacturer_id']) . '"';
		}

		if (isset($data['offset']) || isset($data['limit'])) {
			if ($data['offset'] < 0) {
				$data['offset'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['offset'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalProducts(array $data = array()) {
		$sql = '
			SELECT COUNT(DISTINCT p.`product_id`) AS total
			FROM `' . DB_PREFIX . 'product` p
			LEFT JOIN `' . DB_PREFIX . 'product_description` pd ON (pd.product_id = p.product_id)
			WHERE p.`product_id` > 0
		';

		if (!empty($data['filter_name'])) {
			$sql .= ' AND pd.`name` LIKE "%' . $this->db->escape($data['filter_name']) . '%"';
		}

		if (!empty($data['filter_quantity'])) {
			$sql .= ' AND p.`quantity` <= "' . intval($data['filter_quantity']) . '"';
		}

		if (!empty($data['filter_status'])) {
			$sql .= ' AND p.`status` = "' . !!$data['filter_status'] . '"';
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= ' AND p.`date_added` <= DATE("' . $this->db->escape($data['filter_date_added']) . '")';
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= ' AND p.`date_modified` <= DATE("' . $this->db->escape($data['filter_date_modified']) . '")';
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= ' AND p.`manufacturer_id` = "' . intval($data['filter_manufacturer_id']) . '"';
		}

		$query = $this->db->query($sql);

		return $query->num_rows ? intval($query->row['total']) : 0;
	}

	public function getProductAttributes(int $product_id) {
		$query = $this->db->query('
			SELECT pa.*, l.code
			FROM `' . DB_PREFIX . 'product_attribute` pa
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = pa.language_id)
			WHERE pa.`product_id` = "' . $product_id . '"
		');

		return $query->rows;
	}

	public function getProductDescriptions(int $product_id) {
		$query = $this->db->query('
			SELECT pd.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'product_description` pd
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = pd.language_id)
			WHERE pd.`product_id` = "' . $product_id . '"
		');

		if (!$query->num_rows) {
			return array();
		}

		$result = array();

		foreach ($query->rows as $key => $row) {
			$result['name'][$row['language_code']] = $row['name'];
			$result['description'][$row['language_code']] = $row['description'];
			$result['tag'][$row['language_code']] = empty($row['tag']) ? [] : explode(',', $row['tag']);
			$result['meta_title'][$row['language_code']] = $row['meta_title'];
			$result['meta_description'][$row['language_code']] = $row['meta_description'];
			$result['meta_keyword'][$row['language_code']] = $row['meta_keyword'];
		}

		return $result;
	}

	public function getProductFilters(int $product_id) {
		$query = $this->db->query('
			SELECT `filter_id`
			FROM `' . DB_PREFIX . 'product_filter`
			WHERE `product_id` = "' . $product_id . '"
		');

		return $query->rows;
	}

	public function getProductRecurrings(int $product_id) {
		$query = $this->db->query('
			SELECT `recurring_id`, `customer_group_id`
			FROM `' . DB_PREFIX . 'product_recurring`
			WHERE `product_id` = "' . intval($product_id) . '"
		');

		return $query->rows;
	}

	public function getProductsRelated(int $product_id) {
		$query = $this->db->query('
			SELECT `related_id`
			FROM `' . DB_PREFIX . 'product_related`
			WHERE `product_id` = "' . $product_id . '"
		');

		return $query->rows;
	}

	public function getProductsReward(int $product_id) {
		$query = $this->db->query('
			SELECT `customer_group_id`, `points`
			FROM `' . DB_PREFIX . 'product_reward`
			WHERE `product_id` = "' . intval($product_id) . '"
		');

		return $query->rows;
	}

	public function getProductSpecial(int $product_id) {
		$query = $this->db->query('
			SELECT `customer_group_id`, `priority`, `price`, `date_start`, `date_end`
			FROM `' . DB_PREFIX . 'product_special`
			WHERE `product_id` = "' . intval($product_id) . '"
		');

		return $query->rows;
	}

	public function getProductDiscounts(int $product_id) {
		$query = $this->db->query('
			SELECT `customer_group_id`, `priority`, `price`, `quantity`, `date_start`, `date_end`
			FROM `' . DB_PREFIX . 'product_discount`
			WHERE `product_id` = "' . intval($product_id) . '"
		');

		return $query->rows;
	}

	public function getProductCategories(int $product_id) {
		$query = $this->db->query('
			SELECT `category_id`
			FROM `' . DB_PREFIX . 'product_to_category`
			WHERE `product_id` = "' . intval($product_id) . '"
		');

		return $query->rows;
	}

	public function getProductDownloads(int $product_id) {
		$query = $this->db->query('
			SELECT `download_id`
			FROM `' . DB_PREFIX . 'product_to_download`
			WHERE `product_id` = "' . intval($product_id) . '"
		');

		return $query->rows;
	}

	public function getProductStores(int $product_id) {
		$query = $this->db->query('
			SELECT `store_id`
			FROM `' . DB_PREFIX . 'product_to_store`
			WHERE `product_id` = "' . $product_id . '"
		');

		return $query->rows;
	}

	public function getProductOptions(int $product_id) {
		$query = $this->db->query('
			SELECT po.`option_id`, po.`value`, po.`required`, o.`type`, po.`product_option_id`
			FROM `' . DB_PREFIX . 'product_option` po
			LEFT JOIN `' . DB_PREFIX . 'option` o ON (o.`option_id` = po.`option_id`)
			WHERE po.`product_id` = "' . $product_id . '"
		');

		return $query->rows;
	}

	public function getProductOptionValues(int $product_option_id) {
		$query = $this->db->query('
			SELECT
				pov.option_value_id,
				pov.sku,
				pov.quantity,
				pov.subtract,
				pov.price_prefix,
				pov.price,
				pov.points_prefix,
				pov.points,
				pov.weight_prefix,
				pov.weight
			FROM `' . DB_PREFIX . 'product_option_value` pov
			WHERE pov.product_option_id = "' . $product_option_id . '";
		');

		return $query->rows;
	}

	public function getProductImages(int $product_id) {
		$query = $this->db->query('
			SELECT `image` FROM `' . DB_PREFIX . 'product_image`
			WHERE `product_id` = "' . $product_id . '"
		');

		return $query->rows;
	}

	public function getProductSeoUrls(int $product_id) {
		$query = $this->db->query('
			SELECT su.`keyword`, l.`code` AS language_code
			FROM `' . DB_PREFIX . 'seo_url` su
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.`language_id` = su.`language_id`)
			WHERE `query` = "product_id=' . $product_id . '";
		');

		return $query->rows;
	}

	public function deleteProduct(int $product_id) {
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_attribute` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_description` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_discount` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_filter` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_image` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_option` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_option_value` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_related` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_related` WHERE `related_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_reward` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_special` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_to_category` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_to_download` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_to_layout` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_to_store` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_recurring` WHERE `product_id` = "'. $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'review` WHERE `product_id` = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'seo_url` WHERE `query` = "product_id="' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'coupon_product` WHERE `product_id` = "' . $product_id . '"');

		$this->cache->delete('product');
	}

	/**
	 * Checks if one or more products exists, through the ID
	 *
	 * @param int[] $data
	 *
	 * @return bool|array Returns true/false or an array with ID differences
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

	/**
	 * Saves the details of attributes, description, discounts, promotions, options etc.
	 *
	 * @param int $product_id
	 * @param \stdClass $product
	 *
	 * @return void
	 */
	private function saveData(int $product_id, $product) {
		// Register Product Description
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_description` WHERE product_id = "' . $product_id . '"');

		foreach ($this->config->get('languages') as $language_code => $language) {
			if (isset($product->name[$language_code])) {
				$product_name = $product->name[$language_code];
			} else {
				$product_name = $product->name["default"];
			}

			if (isset($product->description[$language_code])) {
				$product_description = htmlspecialchars($product->description[$language_code], ENT_COMPAT, 'UTF-8');
			} elseif (isset($product->description["default"])) {
				$product_description = htmlspecialchars($product->description["default"], ENT_COMPAT, 'UTF-8');
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

			if (isset($product->meta_description[$language_code])) {
				$product_meta_description = $product->meta_description[$language_code];
			} elseif (isset($product->meta_description["default"])) {
				$product_meta_description = $product->meta_description["default"];
			} else {
				$product_meta_description = "";
			}

			$this->db->query('
				INSERT INTO `' . DB_PREFIX . 'product_description`
				SET `product_id` = "' . intval($product_id) . '",
					`language_id` = "' . intval($language['language_id']) . '",
					`name` = "' . $this->db->escape($product_name) . '",
					`description` = "' . $this->db->escape($product_description) . '",
					`tag` = "' . $this->db->escape($product_tags) . '",
					`meta_title` = "' . $this->db->escape($product_meta_title) . '",
					`meta_description` = "' . $this->db->escape($product_meta_description) . '",
					`meta_keyword` = "' . $this->db->escape($product_meta_keyword) . '"
			');
		}

		// Register in Stores
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_to_store` WHERE product_id = "' . $product_id . '"');

		foreach ($product->stores as $store_id) {
			$this->db->query('
				INSERT INTO `' . DB_PREFIX . 'product_to_store`
				SET `product_id` = "' . intval($product_id) . '",
					`store_id` = "' . intval($store_id) . '"
			');
		}

		// Register Attributes
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_attribute` WHERE product_id = "' . $product_id . '"');

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

		// Register Discounts
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_discount` WHERE product_id = "' . $product_id . '"');

		if (isset($product->discounts)) {
			foreach ($product->discounts as $discount) {
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

		// Register Filters
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_filter` WHERE product_id = "' . $product_id . '"');

		if (isset($product->filters)) {
			foreach ($product->filters as $filter_id) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_filter`
					SET `product_id` = "' . $product_id . '",
						`filter_id` = "' . intval($filter_id) . '"
				');
			}
		}

		// Register Additional Images
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_image` WHERE product_id = "' . $product_id . '"');

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

		// Register Options
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_option` WHERE product_id = "' . $product_id . '"');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_option_value` WHERE product_id = "' . $product_id . '"');

		if (isset($product->options)) {
			foreach ($product->options as $option) {
				if (isset($option->value)) {
					$value = $option->value;
				} else {
					$value = '';
				}

				if (isset($option->required)) {
					$required = intval(!!$option->required);
				} else {
					$required = 0;
				}

				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_option`
					SET `product_id` = "' . $product_id . '",
						`option_id` = "' . intval($option->option_id) . '",
						`value` = "' . $this->db->escape($value) . '",
						`required` = "' . $required . '"
				');

				$product_option_id = intval($this->db->getLastId());

				if (in_array($option->type, ['radio', 'checkbox', 'select'])) {
					foreach ($option->values as $option_value) {
						if (isset($option_value->price)) {
							$price_value = $option_value->price->value ? $option_value->price->value : 0;
							$price_prefix = isset($option_value->price->prefix) ? $option_value->price->prefix : '+';
						} else {
							$price_value = 0;
							$price_prefix = '+';
						}

						if (isset($option_value->points)) {
							$points_value = $option_value->points->value ? $option_value->points->value : 0;
							$points_prefix = isset($option_value->points->prefix) ? $option_value->points->prefix : '+';
						} else {
							$points_value = 0;
							$points_prefix = '+';
						}

						if (isset($option_value->weight)) {
							$weight_value = $option_value->weight->value ? $option_value->weight->value : 0;
							$weight_prefix = isset($option_value->weight->prefix) ? $option_value->weight->prefix : '+';
						} else {
							$weight_value = 0;
							$weight_prefix = '+';
						}

						$this->db->query('
							INSERT INTO `' . DB_PREFIX . 'product_option_value`
							SET `product_option_id` = "' . $product_option_id . '",
								`product_id` = "' . $product_id . '",
								`option_id` = "' . intval($option->option_id) . '",
								`option_value_id` = "' . intval($option_value->option_value_id) . '",
								`sku` = "' . $this->db->escape($option_value->sku) . '",
								`quantity` = "' . intval($option_value->quantity) . '",
								`subtract` = "' . intval($option_value->subtract) . '",
								`price` = "' . floatval($price_value) . '",
								`price_prefix` = "' . $this->db->escape($price_prefix) . '",
								`points` = "' . floatval($points_value) . '",
								`points_prefix` = "' . $this->db->escape($points_prefix) . '",
								`weight` = "' . floatval($weight_value) . '",
								`weight_prefix` = "' . $this->db->escape($weight_prefix) . '"
						');
					}
				}
			}
		}

		// Register Recurrings
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_recurring` WHERE product_id = "' . $product_id . '"');

		if (isset($product->recurring)) {
			foreach ($product->recurring as $recurring) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_recurring`
					SET `product_id` = "' . $product_id . '",
						`recurring_id` = "' . intval($recurring->recurring_id) . '",
						`customer_group_id` = "' . intval($recurring->customer_group_id) . '"
				');
			}
		}

		// Register Related
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_related` WHERE product_id = "' . $product_id . '"');

		if (isset($product->product_related)) {
			foreach ($product->product_related as $related_id) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_related`
					SET `product_id` = "' . intval($product_id) . '",
						`related_id` = "' . intval($related_id) . '"
				');
			}
		}

		// Register Points
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_reward` WHERE product_id = "' . $product_id . '"');

		if (isset($product->points_reward)) {
			foreach ($product->points_reward as $reward) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_reward`
					SET `product_id` = "' . intval($product_id) . '",
						`customer_group_id` = "' . intval($reward->customer_group_id) . '",
						`points` = "' . intval($reward->points) . '"
				');
			}
		}

		// Register Special
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_special` WHERE product_id = "' . $product_id . '"');

		if (isset($product->special)) {
			foreach ($product->special as $special) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_special`
					SET `product_id` = "' . $product_id . '",
						`customer_group_id` = "' . intval($special->customer_group_id) . '",
						`priority` = "' . intval($special->priority) . '",
						`price` = "' . floatval($special->price) . '",
						`date_start` = "' . $this->db->escape($special->date_start) . '",
						`date_end` = "' . $this->db->escape($special->date_end) . '"
				');
			}
		}

		// Register Categories
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_to_category` WHERE product_id = "' . $product_id . '"');

		if (isset($product->categories)) {
			foreach ($product->categories as $category_id) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_to_category`
					SET `product_id` = "' . $product_id . '",
						`category_id` = "' . intval($category_id) . '"
				');
			}
		}

		// Register Downloads
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_to_download` WHERE product_id = "' . $product_id . '"');

		if (isset($product->downloads)) {
			foreach ($product->downloads as $download_id) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_to_download`
					SET `product_id` = "' . $product_id . '",
						`download_id` = "' . intval($download_id) . '"
				');
			}
		}

		// Register SEO URL
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'seo_url` WHERE product_id = "' . $product_id . '"');

		if (isset($product->seo_url_generate)) {
			$seo_url_generate_auto = isset($product->seo_url_generate->auto) ? !!$product->seo_url_generate->auto : false;

			if ($seo_url_generate_auto) {
				$seo_url_suffix = isset($product->seo_url_generate->suffix) ? !!$product->seo_url_generate->suffix : false;
				$seo_url_prefix = isset($product->seo_url_generate->prefix) ? !!$product->seo_url_generate->prefix : false;

				foreach ($this->config->get('languages') as $key => $language) {
					$language_code = $language['code'];

					if (isset($product->name[$language_code])) {
						$product_name = $product->name[$language_code];
					} else {
						$product_name = $product->name["default"];
					}

					$seo_url[$language['language_id']] = slug("{$seo_url_prefix}{$product_name}{$seo_url_suffix}");
				}

				foreach ($product->stores as $store_id) {
					foreach ($seo_url as $language_id => $query) {
						$query_has_exist = !!$this->db->query('
							SELECT seo_url_id
							FROM `' . DB_PREFIX . 'seo_url`
							WHERE `keyword` = "' . $this->db->escape($query) . '"
						')->num_rows;

						if ($query_has_exist) {
							$query .= "-{$product_id}";
						}

						$this->db->query('
							INSERT INTO `' . DB_PREFIX . 'seo_url`
							SET `store_id` = "' . intval($store_id) . '",
								`language_id` = "' . intval($language_id) . '",
								`query` = "product_id=' . $product_id . '",
								`keyword` = "' . $this->db->escape($query) . '"
						');
					}
				}
			}
		}

		$this->cache->delete('product');
	}
}
