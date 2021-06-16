<?php
class ModelCatalogManufacturer extends Model {
	public function getManufacturer(int $manufacturer_id) {
		$cache_key = 'api_manufacturer_id_' . $manufacturer_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . $manufacturer_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}
}
