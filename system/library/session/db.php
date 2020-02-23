<?php
namespace Session;
final class DB {
	private $expire;

	public function __construct($registry) {
		$this->db = $registry->get('db');

		if (ini_get('session.gc_maxlifetime')) {
			$gc_maxlifetime = ini_get('session.gc_maxlifetime');
		} else {
			$gc_maxlifetime = 3600;
		}

		$this->expire = $gc_maxlifetime;
	}

	public function exists($session_id) {
		$query = $this->db->query("SELECT `session_id` FROM `" . DB_PREFIX . "session` WHERE session_id = '" . $this->db->escape($session_id) . "'");

		if ($query->num_rows) {
			return true;
		}

		return false;
	}

	public function read($session_id) {
		$query = $this->db->query("SELECT `data` FROM `" . DB_PREFIX . "session` WHERE session_id = '" . $this->db->escape($session_id) . "' AND expire > NOW()");

		if ($query->num_rows) {
			return json_decode($query->row['data'], true);
		}

		return array();
	}

	public function write($session_id, $data) {
		if ($session_id) {
			$this->db->query("REPLACE INTO `" . DB_PREFIX . "session` SET session_id = '" . $this->db->escape($session_id) . "', `data` = '" . $this->db->escape(json_encode($data)) . "', expire = DATE_ADD(NOW(), INTERVAL ". $this->db->escape($this->expire) ." SECOND)");
		}

		return true;
	}

	public function destroy($session_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "session` WHERE session_id = '" . $this->db->escape($session_id) . "'");

		return true;
	}
}
