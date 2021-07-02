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
			$product->dimensions->height = 0;
			$product->dimensions->length_class_id = $this->config->get('config_length_class_id');
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

		return $product;
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

		/** Register Product */
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
	}

	public function getProduct(int $product_id) {
		$query = $this->db->query('
			SELECT DISTINCT *
			FROM `' . DB_PREFIX . 'product`
			WHERE `product_id` = "' . $product_id . '"');

		return $query->row;
	}

	public function getProductAttributes(int $product_id) {
		$query = $this->db->query('
			SELECT pa.*, l.code
			FROM `' . DB_PREFIX . 'product_attribute` pa
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = pa.language_id)
			WHERE pa.`product_id` = "' . $product_id . '"');

		if (!$query->num_rows) {
			return array();
		}

		$result = array();

		foreach ($query->rows as $key => $row) {
			$result[$row['attribute_id']]['attribute_id'] = $row['attribute_id'];
			$result[$row['attribute_id']][$row['code']] = $row['text'];
		}

		return array_values($result);
	}

	public function getProductDescriptions(int $product_id) {
		$query = $this->db->query('
			SELECT pd.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'product_description` pd
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = pd.language_id)
			WHERE pd.`product_id` = "' . $product_id . '"');

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

	public function getProductDiscounts(int $product_id) {
		$query = $this->db->query('
			SELECT * FROM `' . DB_PREFIX . 'product_discount`
			WHERE `product_id` = "' . $product_id . '"
		');

		$result = array();

		foreach ($query->rows as $row) {
			$result[] = [
				'customer_group_id' => intval($row['customer_group_id']),
				'price' => floatval($row['price']),
				'priority' => intval($row['priority']),
				'date_start' => $row['date_start'],
				'date_end' => $row['date_end'],
				'quantity' => intval($row['quantity']),
			];
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
		/* $query = $this->db->query('
			SELECT ``
		'); */
	}

	public function getProductImages(int $product_id) {
		$query = $this->db->query('
			SELECT `image` FROM `' . DB_PREFIX . 'product_image`
			WHERE `product_id` = "' . $product_id . '"
		');

		return $query->rows;
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

	/**
	 * Salva os detalhes de atributos, descrição, descontos, promoções, opções etc.
	 *
	 * @param int $product_id
	 * @param \stdClass $product
	 *
	 * @return void
	 */
	private function saveData(int $product_id, $product) {
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

		/** Register Options */
		if (isset($product->options)) {
			foreach ($product->options as $option) {
				if (isset($option->value)) {
					$value = $option->value;
				} else {
					$value = '';
				}

				if (isset($option->required)) {
					$required = $option->required ? 1 : 0;
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

		/** Register Recurrings */
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

		/** Register Related */
		if (isset($product->product_related)) {
			foreach ($product->product_related as $related_id) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_related`
					SET `product_id` = "' . intval($product_id) . '",
						`related_id` = "' . intval($related_id) . '"
				');
			}
		}

		/** Register Points */
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

		/** Register Special */
		if (isset($product->special)) {
			foreach ($product->special as $special) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_special`
					SET `product_id` = "' . $product_id . '",
						`customer_group_id` = "' . intval($special->customer_group_id) . '",
						`priority` = "' . intval($special->priority) . '",
						`price` = "' . intval($special->price) . '",
						`date_start` = "' . $this->db->escape($special->date_start) . '",
						`date_end` = "' . $this->db->escape($special->date_end) . '"
				');
			}
		}

		/** Register Categories */
		if (isset($product->categories)) {
			foreach ($product->categories as $category_id) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_to_category`
					SET `product_id` = "' . $product_id . '",
						`category_id` = "' . intval($category_id) . '"
				');
			}
		}

		/** Register Downloads */
		if (isset($product->downloads)) {
			foreach ($product->downloads as $download_id) {
				$this->db->query('
					INSERT INTO `' . DB_PREFIX . 'product_to_download`
					SET `product_id` = "' . $product_id . '",
						`download_id` = "' . intval($download_id) . '"
				');
			}
		}

		/** Register SEO URL */
		if (isset($product->seo_url_generate)) {
			$seo_url_generate_auto = isset($product->seo_url_generate->auto) ? !!$product->seo_url_generate->auto : false;

			if ($seo_url_generate_auto) {
				$seo_url_suffix = isset($product->seo_url_generate->suffix) ? !!$product->seo_url_generate->suffix : false;
				$seo_url_prefix = isset($product->seo_url_generate->prefix) ? !!$product->seo_url_generate->prefix : false;

				foreach ($this->config->get('languages') as $language_code => $language) {
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
	}
}
