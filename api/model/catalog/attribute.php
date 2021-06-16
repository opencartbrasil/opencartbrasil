<?php
class ModelCatalogAttribute extends Model {
	public function getHasAttributeById(array $data = array()) {
		$attributes_id = array_map('intval', $data);

		$result = $this->db->query('SELECT attribute_id FROM `' . DB_PREFIX . 'product_attribute` WHERE `attribute_id` IN (' . implode(',', $attributes_id) . ') AND `language_id` = "' . intval($this->config->get('config_language_id')) . '"');

		if (count($attributes_id) > $result->num_rows) {
			$attributes_founds = array_map(function($item) {
				return $item['attribute_id'];
			}, $result->rows);

			return array_diff($attributes_id, $attributes_founds);
		}

		return true;
	}
}
