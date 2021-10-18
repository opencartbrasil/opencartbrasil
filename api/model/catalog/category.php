<?php
class ModelCatalogCategory extends Model {
	public function getHasCategoryById(array $data = array()) {
		$categories_id = array_map('intval', $data);

		$result = $this->db->query('SELECT category_id FROM `' . DB_PREFIX . 'category` WHERE `category_id` IN (' . implode(',', $categories_id) . ')');

		if (count($categories_id) > $result->num_rows) {
			$categories_founds = array_map(function($item) {
				return $item['category_id'];
			}, $result->rows);

			return array_diff($categories_id, $categories_founds);
		}

		return true;
	}

	public function getCategories(array $data = array()) {
		$sql = '
			SELECT
				c.*,
				CONVERT_TZ(c.`date_added`, @@time_zone, "+00:00") AS `date_added`,
				CONVERT_TZ(c.`date_modified`, @@time_zone, "+00:00") AS `date_modified`,
				(SELECT COUNT(*) FROM `' . DB_PREFIX . 'product_to_category` ptc WHERE ptc.`category_id` = c.`category_id`) AS total_products
			FROM `' . DB_PREFIX . 'category` c
			WHERE c.category_id > 0
		';

		if ($data['filter_parent_id'] !== null) {
			$sql .= ' AND c.parent_id = ' . (int)$data['filter_parent_id'] . '';
		}

		if ($data['filter_total_products_eq'] !== null) {
			$sql .= ' HAVING(total_products = ' . (int)$data['filter_total_products_eq'] . ')';
		} elseif (isset($data['filter_total_products_lt']) || isset($data['filter_total_products_gt'])) {

			if (isset($data['filter_total_products_lt'])) {
				$filter_total_products_lt = intval($data['filter_total_products_lt']);
			} else {
				$filter_total_products_lt = 4294967295;
			}

			if (isset($data['filter_total_products_gt'])) {
				$filter_total_products_gt = intval($data['filter_total_products_gt']);
			} else {
				$filter_total_products_gt = 0;
			}

			$sql .= ' HAVING(total_products < ' . $filter_total_products_lt . ' AND total_products > ' . $filter_total_products_gt . ')';
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

	public function getCategoryDescriptionById(int $category_id) {
		$sql = '
			SELECT
				cd.`name`,
				cd.`description`,
				cd.`meta_title`,
				cd.`meta_description`,
				cd.`meta_keyword`,
				l.`code` AS language_code
			FROM `' . DB_PREFIX . 'category_description` cd
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (cd.`language_id` = l.`language_id`)
			WHERE cd.category_id = "' . $category_id . '"
		';

		$query = $this->db->query($sql);

		$result = array();

		foreach ($query->rows as $row) {
			$language_code = $row['language_code'];

			$result['name'][$language_code] = $row['name'];
			$result['description'][$language_code] = $row['description'];
			$result['meta_title'][$language_code] = $row['meta_title'];
			$result['meta_description'][$language_code] = $row['meta_description'];
			$result['meta_keyword'][$language_code] = $row['meta_keyword'];
		}

		return $result;
	}

	public function getTotalCategories(array $data = array()) {
		$sql = '
			SELECT
				c.category_id,
				(SELECT COUNT(*) FROM `' . DB_PREFIX . 'product_to_category` ptc WHERE ptc.`category_id` = c.`category_id`) AS total_products
			FROM `' . DB_PREFIX . 'category` c
			WHERE c.category_id > 0
		';

		if ($data['filter_parent_id'] !== null) {
			$sql .= ' AND c.parent_id = ' . (int)$data['filter_parent_id'] . '';
		}

		if ($data['filter_total_products_eq'] !== null) {
			$sql .= ' HAVING(total_products = ' . (int)$data['filter_total_products_eq'] . ')';
		} elseif (isset($data['filter_total_products_lt']) || isset($data['filter_total_products_gt'])) {
			if (isset($data['filter_total_products_lt'])) {
				$filter_total_products_lt = intval($data['filter_total_products_lt']);
			} else {
				$filter_total_products_lt = 4294967295;
			}

			if (isset($data['filter_total_products_gt'])) {
				$filter_total_products_gt = intval($data['filter_total_products_gt']);
			} else {
				$filter_total_products_gt = 0;
			}

			$sql .= ' HAVING(total_products < ' . $filter_total_products_lt . ' AND total_products > ' . $filter_total_products_gt . ')';
		}

		$query = $this->db->query($sql);

		return $query->num_rows ? $query->num_rows : 0;
	}
}
