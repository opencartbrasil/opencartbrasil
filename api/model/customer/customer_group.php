<?php
class ModelCustomerCustomerGroup extends Model {
	public function getCustomerGroup($customer_group_id) {
		$cache_key = 'api_customer_group_id_' . $customer_group_id;

		$result = $this->cache->get($cache_key);

		if (!$result) {
			$query = $this->db->query("
				SELECT DISTINCT * FROM `" . DB_PREFIX . "customer_group` cg
				LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (cg.customer_group_id = cgd.customer_group_id)
				WHERE cg.customer_group_id = '" . (int)$customer_group_id . "'
			");

			$result = $query->row;

			$this->cache->set($cache_key, $result);
		}

		return $result;
	}
}
