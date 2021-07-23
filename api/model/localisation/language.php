<?php
class ModelLocalisationLanguage extends Model {
	public function getLanguage(int $language_id) {
		$query = $this->db->query('
			SELECT DISTINCT *
			FROM `' . DB_PREFIX . 'language`
			WHERE language_id = "' . $language_id . '"
		');

		return $query->row;
	}

	public function getLanguages(array $data = array()) {
		$sql = '
			SELECT DISTINCT *
			FROM `' . DB_PREFIX . 'language`
			WHERE `language_id` > 0
		';

		if (!empty($data['filter_code'])) {
			$sql .= ' AND `code` = "' . $this->db->escape($data['filter_code']) . '"';
		}

		if (!empty($data['filter_status'])) {
			$sql .= ' AND `status` = "' . $this->db->escape($data['filter_status']) . '"';
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

	public function getTotalLanguages($filter_data = array()) {
		$sql = '
			SELECT DISTINCT *
			FROM `' . DB_PREFIX . 'language`
			WHERE `language_id` > 0
		';

		if (!empty($filter_data['filter_code'])) {
			$sql .= ' AND `code` = "' . $this->db->escape($filter_data['filter_code']) . '"';
		}

		if (!empty($filter_data['filter_status'])) {
			$sql .= ' AND `status` = "' . !!$filter_data['filter_status'] . '"';
		}

		$query = $this->db->query($sql);

		return $query->num_rows;
	}

	public function getLanguageByCode($code) {
		$query = $this->db->query('
			SELECT * FROM `' . DB_PREFIX . 'language`
			WHERE code = "' . $this->db->escape($code) . '"
		');

		return $query->row;
	}
}
