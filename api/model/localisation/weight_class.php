<?php
class ModelLocalisationWeightClass extends Model {
	public function getWeightClass($weight_class_id) {
		$cache_key = 'api_weight_class_id_' . $stock_status_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "weight_class lc
				LEFT JOIN " . DB_PREFIX . "weight_class_description lcd
				ON (lc.weight_class_id = lcd.weight_class_id)
				WHERE lc.weight_class_id = '" . (int)$weight_class_id . "'
					AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getWeightClassIdByUnit($weight_class_unit) {
		$cache_key = 'api_weight_class_unit_' . $stock_status_unit;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("
				SELECT lc.weight_class_id
				FROM " . DB_PREFIX . "weight_class_description lcd
				LEFT JOIN " . DB_PREFIX . "weight_class lc
				ON (lcd.weight_class_id = lc.weight_class_id)
				WHERE lcd.unit = '" . $this->db->escape($weight_class_unit) . "'
					AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}
}
