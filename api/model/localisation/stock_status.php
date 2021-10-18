<?php
class ModelLocalisationStockStatus extends Model {
	public function getStockStatus(int $stock_status_id) {
		$cache_key = 'api_stock_status_id_' . $stock_status_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stock_status` WHERE stock_status_id = '" . $stock_status_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getStockStatuses($data = array()) {
		$sql = '
			SELECT ss.*, l.code AS language_code FROM `' . DB_PREFIX . 'stock_status` ss
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = ss.language_id)
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

	public function getTotalStockStatuses() {
		$query = $this->db->query('SELECT COUNT(DISTINCT stock_status_id) AS total FROM `' . DB_PREFIX . 'stock_status`');

		return $query->row['total'];
	}
}
