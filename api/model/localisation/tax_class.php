<?php
class ModelLocalisationTaxClass extends Model {
	public function getTaxClass(int $tax_class_id) {
		$cache_key = 'api_tax_class_id_' . $tax_class_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tax_class` WHERE tax_class_id = '" . $tax_class_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getTaxClasses($data = array()) {
		$sql = '
			SELECT *,
				CONVERT_TZ(`date_added`, @@time_zone, "+00:00") AS date_added,
				CONVERT_TZ(`date_modified`, @@time_zone, "+00:00") AS date_modified
			FROM `' . DB_PREFIX . 'tax_class`
		';

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

	public function getTotalTaxClasses() {
		$query = $this->db->query('SELECT COUNT(*) AS total FROM `' . DB_PREFIX . 'tax_class`');

		return $query->row['total'];
	}
}
