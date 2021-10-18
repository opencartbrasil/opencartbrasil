<?php
class ModelLocalisationOrderStatus extends Model {
	public function getOrderStatus(int $order_status_id) {
		$cache_key = 'api_order_status_id_' . $order_status_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status` WHERE order_status_id = '" . $order_status_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function getOrderStatuses($data = array()) {
		$sql = '
			SELECT os.*, l.code AS language_code FROM `' . DB_PREFIX . 'order_status` os
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = os.language_id)
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

	public function getTotalOrderStatuses() {
		$query = $this->db->query('SELECT COUNT(DISTINCT order_status_id) AS total FROM `' . DB_PREFIX . 'order_status`');

		return $query->row['total'];
	}
}
