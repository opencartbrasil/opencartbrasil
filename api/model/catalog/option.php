<?php
class ModelCatalogOption extends Model {
	public function getOption(int $option_id) {
		$cache_key = 'api_option_id_' . $option_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE o.option_id = '" . (int)$option_id . "'");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}

	public function optionValueIsRelatedToOptionId(int $option_id, int $option_value_id) {
		$cache_key = sprintf('api_option_%d_option_value_%d', $option_id, $option_value_id);

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "option opt
				LEFT JOIN " . DB_PREFIX . "option_value optv ON (optv.option_id = opt.option_id)
				LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (optv.option_value_Id = ovd.option_value_id)
				WHERE opt.option_id = '" . $option_id . "'
					AND optv.option_value_id = '" . $option_value_id . "'
				;
			");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}
}
