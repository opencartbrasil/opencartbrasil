<?php
class ModelCronSession extends Model {
	public function deleteExpires() {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "session` WHERE expire < NOW()");
	}
}
