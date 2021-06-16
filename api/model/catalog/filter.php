<?php
class ModelCatalogFilter extends Model {
	public function getHasFilterById(array $data = array()) {
		$filters_id = array_map('intval', $data);

		$result = $this->db->query('SELECT filter_id FROM `' . DB_PREFIX . 'filter` WHERE `filter_id` IN (' . implode(',', $filters_id) . ')');

		if (count($filters_id) > $result->num_rows) {
			$categories_founds = array_map(function($item) {
				return $item['filter_id'];
			}, $result->rows);

			return array_diff($filters_id, $categories_founds);
		}

		return true;
	}
}
