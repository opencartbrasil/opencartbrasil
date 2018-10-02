<?php
class ModelUpdate02 extends Model {
	public function update() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "affiliate`;");
	}
}