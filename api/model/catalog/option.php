<?php
class ModelCatalogOption extends Model {
	public function getOption(int $option_id) {
		$cache_key = 'api_option_id_' . $option_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE o.option_id = '" . (int)$option_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getOptionValueIsRelatedToOptionId(int $option_id, int $option_value_id) {
		$cache_key = sprintf('api_option_%d_option_value_%d', $option_id, $option_value_id);

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("
				SELECT *
				FROM `" . DB_PREFIX . "option` opt
				LEFT JOIN `" . DB_PREFIX . "option_value` optv ON (optv.option_id = opt.option_id)
				LEFT JOIN `" . DB_PREFIX . "option_value_description` ovd ON (optv.option_value_Id = ovd.option_value_id)
				WHERE opt.option_id = '" . $option_id . "'
					AND optv.option_value_id = '" . $option_value_id . "'
				;
			");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getOptions(array $data = array()) {
		$sql = '
			SELECT *
			FROM `' . DB_PREFIX . 'option` o
			LEFT JOIN `' . DB_PREFIX . 'option_description` od ON (o.option_id = od.option_id)
			WHERE o.option_id > 0
		';

		if (!empty($data['filter_name'])) {
			$sql .= ' AND od.name LIKE "%' . $this->db->escape($data['filter_name']) . '%"';
		}

		if (!empty($data['filter_type'])) {
			$sql .= ' AND o.type = "' . $this->db->escape($data['filter_type']) . '"';
		}

		$sql .= ' GROUP BY o.option_id';

		$sort_data = array(
			'od.name',
			'o.type',
			'o.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= ' ORDER BY ' . $data['sort'];
		} else {
			$sql .= ' ORDER BY o.sort_order';
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= ' DESC';
		} else {
			$sql .= ' ASC';
		}

		if (isset($data['offset']) || isset($data['limit'])) {
			if ($data['offset'] < 0) {
				$data['offset'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= ' LIMIT ' . (int)$data['offset'] . ',' . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOptionDescriptions(int $option_id) {
		$option_data = array();

		$query = $this->db->query('
			SELECT od.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'option_description` od
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = od.language_id)
			WHERE od.option_id = "' . $option_id . '"
		');

		foreach ($query->rows as $result) {
			$option_data[$result['language_code']] = $result['name'];
		}

		return $option_data;
	}

	public function getOptionValue(int $option_value_id) {
		$query = $this->db->query('
			SELECT *
			FROM `' . DB_PREFIX . 'option_value` ov
			WHERE ov.option_id = "' . $option_value_id . '"
			ORDER BY sort_order ASC;
		');

		return $query->row;
	}

	public function getOptionValues(int $option_id) {
		$option_value_data = array();

		$option_value_query = $this->db->query('
			SELECT ov.option_value_id, ov.image, ov.sort_order
			FROM `' . DB_PREFIX . 'option_value` ov
			WHERE ov.option_id = "' . $option_id . '"
			ORDER BY ov.sort_order
		');

		foreach ($option_value_query->rows as $option_value) {
			$option_value_data[] = array(
				'option_value_id' => intval($option_value['option_value_id']),
				'image' => $option_value['image'],
				'sort_order' => intval($option_value['sort_order'])
			);
		}

		return $option_value_data;
	}

	public function getOptionValueDescriptions(int $option_id) {
		$result = array();

		$query = $this->db->query('
			SELECT ovd.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'option_value_description` ovd
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = ovd.language_id)
			WHERE ovd.option_value_id = "' . intval($option_id) . '"
		');

		foreach ($query->rows as $option_value_description) {
			$result[$option_value_description['language_code']] = $option_value_description['name'];
		}

		return $result;
	}

	public function getTotalOptions(array $data = array()) {
		$sql = '
			SELECT COUNT(DISTINCT o.option_id) AS total
			FROM `' . DB_PREFIX . 'option` o
			LEFT JOIN `' . DB_PREFIX . 'option_description` od ON (o.option_id = od.option_id)
			WHERE o.option_id > 0
		';

		if (!empty($data['filter_name'])) {
			$sql .= ' AND od.name LIKE "%' . $this->db->escape($data['filter_name']) . '%"';
		}

		if (!empty($data['filter_type'])) {
			$sql .= ' AND o.type = "' . $this->db->escape($data['filter_type']) . '"';
		}

		$query = $this->db->query($sql);

		return intval($query->row['total']);
	}
}
