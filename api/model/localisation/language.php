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

	public function getLanguages(array $filter_data = array()) {
		$sql = '
			SELECT DISTINCT *
			FROM `' . DB_PREFIX . 'language`
			WHERE `language_id` > 0
		';

		if (!empty($filter_data['code'])) {
			$sql .= ' AND `code` = "' . $this->db->escape($filter_data['code']) . '"';
		}

		if (isset($filter_data['status'])) {
			$sql .= ' AND `status` = "' . $this->db->escape($filter_data['status']) . '"';
		}

		if (!empty($filter_data['per_page'])) {
			$sql .= ' LIMIT ' . intval($filter_data['per_page']);
		} else {
			$sql .= ' LIMIT ' . intval($this->config->get('db_list_per_page'));
		}

		if (!empty($filter_data['per_page']) && !empty($filter_data['offset'])) {
			$sql .= ' OFFSET ' . intval($filter_data['offset']) . '';
		} else {
			$sql .= ' OFFSET 0';
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

		if (!empty($filter_data['code'])) {
			$sql .= ' AND `code` = "' . $this->db->escape($filter_data['code']) . '"';
		}

		if (!empty($filter_data['status'])) {
			$sql .= ' AND `status` = "' . !!$filter_data['status'] . '"';
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
