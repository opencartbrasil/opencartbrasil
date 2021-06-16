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
}
