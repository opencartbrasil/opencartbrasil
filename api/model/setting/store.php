<?php
class ModelSettingStore extends Model {
	public function getHasStoreById(array $data = array()) {
		$stores_id = array_map('intval', $data);

		$stores_id = array_filter($stores_id, function($store_id) {
			return $store_id !== 0;
		});

		$result = $this->db->query('SELECT store_id FROM `' . DB_PREFIX . 'store` WHERE `store_id` IN (' . implode(',', $stores_id) . ')');

		if (count($stores_id) > $result->num_rows) {
			$stores_founds = array_map(function($item) {
				return $item['store_id'];
			}, $result->rows);

			return array_diff($stores_id, $stores_founds);
		}

		return true;
	}
}
