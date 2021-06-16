<?php
class ModelCatalogRecurring extends Model {
	public function getRecurring($recurring_id) {
		$cache_key = 'api_recurring_id_' . $recurring_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring` WHERE recurring_id = '" . (int)$recurring_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}
}
