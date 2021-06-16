<?php
class ModelLocalisationLengthClass extends Model {
	public function getLengthClass($length_class_id) {
		$cache_key = 'api_length_class_id_' . $length_class_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "length_class lc
				LEFT JOIN " . DB_PREFIX . "length_class_description lcd
				ON (lc.length_class_id = lcd.length_class_id)
				WHERE lc.length_class_id = '" . (int)$length_class_id . "'
					AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getLengthClassIdByUnit($length_class_unit) {
		$cache_key = 'api_length_class_unit_' . $length_class_unit;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("
				SELECT lc.length_class_id
				FROM " . DB_PREFIX . "length_class_description lcd
				LEFT JOIN " . DB_PREFIX . "length_class lc
				ON (lcd.length_class_id = lc.length_class_id)
				WHERE lcd.unit = '" . $length_class_unit . "'
					AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}
}
