<?php
class ModelCatalogManufacturer extends Model {
	public function getManufacturer(int $manufacturer_id) {
		$cache_key = 'api_manufacturer_id_' . $manufacturer_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "manufacturer` WHERE manufacturer_id = '" . $manufacturer_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getManufacturers($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "manufacturer`";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
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

	public function getTotalManufacturers(array $data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "manufacturer`";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$query = $this->db->query($sql);

		return intval($query->row['total']);
	}
}
