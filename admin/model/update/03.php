<?php
class ModelUpdate03 extends Model {
	public function update() {
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` CHANGE `password` `password` VARCHAR(255) NOT NULL");

		$table_query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_option_value' AND COLUMN_NAME = 'sku'");
		if (!$table_query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` ADD `sku` VARCHAR(64) NOT NULL AFTER `option_value_id`;");
		}

		$table_query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "customer_ip' AND COLUMN_NAME = 'store_id'");
		if (!$table_query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_ip` ADD `store_id` INT(11) NOT NULL AFTER `customer_id`;");
		}

		$table_query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "customer_ip' AND COLUMN_NAME = 'country'");
		if (!$table_query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_ip` ADD `country` VARCHAR(2) NOT NULL AFTER `ip`;");
		}
	}
}