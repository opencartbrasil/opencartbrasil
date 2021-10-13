<?php
class ModelLocalisationLengthClass extends Model {
	public function getLengthClass($length_class_id) {
		$cache_key = 'api_length_class_id_' . $length_class_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("
				SELECT *
				FROM `" . DB_PREFIX . "length_class` lc
				LEFT JOIN `" . DB_PREFIX . "length_class_description` lcd
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
				FROM `" . DB_PREFIX . "length_class_description` lcd
				LEFT JOIN `" . DB_PREFIX . "length_class` lc
				ON (lcd.length_class_id = lc.length_class_id)
				WHERE lcd.unit = '" . $length_class_unit . "'
				  AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getLengthClasses(array $data = array()) {
		$sql = '
			SELECT lc.*, lcd.`unit`
			FROM `' . DB_PREFIX . 'length_class` lc
			LEFT JOIN `' . DB_PREFIX . 'length_class_description` lcd ON (lc.`length_class_id` = lcd.`length_class_id`)
			WHERE lc.length_class_id > 0
		';

		if (isset($data['filter_unit'])) {
			$sql .= ' AND lcd.`unit` = "' . $this->db->escape($data['filter_unit']) . '"';
		}

		$sql .= ' GROUP BY lc.length_class_id';

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

	public function getLengthClassDescriptions(int $length_class_id) {
		$length_class_data = array();

		$query = $this->db->query('
			SELECT lcd.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'length_class_description` lcd
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = lcd.language_id)
			WHERE lcd.`length_class_id` = "' . (int)$length_class_id . '"
		');

		foreach ($query->rows as $result) {
			$language_code = $result['language_code'];

			$length_class_data['title'][$language_code] = $result['title'];
			$length_class_data['unit'][$language_code] = $result['unit'];
		}

		return $length_class_data;
	}

	public function getTotalLengthClasses(array $data = array()) {
		$sql = '
			SELECT COUNT(DISTINCT lc.length_class_id) AS total FROM `' . DB_PREFIX . 'length_class` lc
			LEFT JOIN `' . DB_PREFIX . 'length_class_description` lcd ON (lc.`length_class_id` = lcd.`length_class_id`)
			WHERE lc.length_class_id > 0
		';

		if (isset($data['filter_unit'])) {
			$sql .= ' AND lcd.`unit` = "' . $this->db->escape($data['filter_unit']) . '"';
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
