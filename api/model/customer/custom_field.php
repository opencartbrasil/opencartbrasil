<?php
class ModelCustomerCustomField extends Model {
	public function getCustomFieldsCodes() {
		$query = $this->db->query("SELECT `custom_field_id`, `code` FROM `" . DB_PREFIX . "custom_field`");

		$result = array();

		foreach($query->rows as $row) {
			$result[$row['custom_field_id']] = $row['code'];
		};

		return $result;
	}
}
