<?php
class ModelLocalisationTaxClass extends Model {

	public function getTaxClass(int $tax_class_id) {
		$cache_key = 'api_tax_class_id_' . $tax_class_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '" . $tax_class_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}
}
