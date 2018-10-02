<?php
class ModelUpdate04 extends Model {
	public function update() {
		$this->db->query("UPDATE `oc_event` SET `trigger` = 'catalog/model/account/affiliate/addAffiliate/after' WHERE `" . DB_PREFIX . "event`.`event_id` = 10;");

		$this->db->query("UPDATE `oc_event` SET `trigger` = 'catalog/model/account/affiliate/editAffiliate/after' WHERE `" . DB_PREFIX . "event`.`event_id` = 11;");

		$this->db->query("UPDATE `oc_event` SET `trigger` = 'catalog/model/account/affiliate/addAffiliate/after' WHERE `" . DB_PREFIX . "event`.`event_id` = 18;");

		$this->db->query("UPDATE `oc_event` SET `trigger` = 'catalog/model/account/affiliate/addAffiliate/after' WHERE `" . DB_PREFIX . "event`.`event_id` = 19;");
	}
}