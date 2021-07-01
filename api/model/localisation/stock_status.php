<?php
class ModelLocalisationStockStatus extends Model {

	public function getStockStatus(int $stock_status_id) {
		$cache_key = 'api_stock_status_id_' . $stock_status_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . $stock_status_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}
}
