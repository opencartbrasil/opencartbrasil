<?php
namespace Session;
final class DB {
	public $expire = '';

	public function __construct($registry) {
		$this->db = $registry->get('db');

		$this->expire = ini_get('session.gc_maxlifetime');
	}

	public function read($session_id) {
		$query = $this->db->query("SELECT `data` FROM `" . DB_PREFIX . "session` WHERE session_id = '" . $this->db->escape($session_id) . "' AND expire > '" . $this->db->escape(date('Y-m-d H:i:s', time()))  . "'");

		if ($query->num_rows) {
			return json_decode($query->row['data'], true);
		} else {
			return false;
		}
	}

	public function write($session_id, $data) {
		if ($session_id) {
			$this->db->query("REPLACE INTO `" . DB_PREFIX . "session` SET session_id = '" . $this->db->escape($session_id) . "', `data` = '" . $this->db->escape(json_encode($data)) . "', expire = '" . $this->db->escape(date('Y-m-d H:i:s', time() + $this->expire)) . "'");
		}

		return true;
	}

	public function destroy($session_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "session` WHERE session_id = '" . $this->db->escape($session_id) . "'");

		return true;
	}
}
