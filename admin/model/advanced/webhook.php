<?php

class ModelAdvancedWebHook extends Model {
	public function addHook(array $data = array()) {
		$headers = isset($data['headers']) ? $data['headers'] : array();
		$actions = isset($data['actions']) ? $data['actions'] : array();

		$this->db->query("INSERT INTO `" . DB_PREFIX . "webhook_client` SET `description` = '" . $this->db->escape($data['description']) . "', `url` = '" . $this->db->escape($data['url']) . "', `auth_user` = '" . $this->db->escape($data['auth_user']) . "', `auth_password` = '" . $this->db->escape($data['auth_password']) . "', `headers` = '" . $this->db->escape(json_encode($headers)) . "', `actions` = '" . $this->db->escape(json_encode($actions)) . "', `status` = '" . $this->db->escape($data['status']) . "'");
	}

	public function editHook($webhook_client_id, array $data = array()) {
		$headers = isset($data['headers']) ? $data['headers'] : array();
		$actions = isset($data['actions']) ? $data['actions'] : array();

		$this->db->query("UPDATE `" . DB_PREFIX . "webhook_client` SET `description` = '" . $this->db->escape($data['description']) . "', `url` = '" . $this->db->escape($data['url']) . "', `auth_user` = '" . $this->db->escape($data['auth_user']) . "', `auth_password` = '" . $this->db->escape($data['auth_password']) . "', `headers` = '" . $this->db->escape(json_encode($headers)) . "', `actions` = '" . $this->db->escape(json_encode($actions)) . "', `status` = '" . $this->db->escape($data['status']) . "', `date_modified` = NOW() WHERE `webhook_client_id` = '" . (int)$webhook_client_id . "'");
	}

	public function deleteHook($webhook_client_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "webhook_client` WHERE `webhook_client_id` = '" . (int)$webhook_client_id . "'");
	}

	public function getHook($webhook_client_id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "webhook_client` wc WHERE `webhook_client_id` = '" . (int)$webhook_client_id . "'";

		$query = $this->db->query($sql);

		return array(
			'webhook_client_id' => $query->row['webhook_client_id'],
			'description' => $query->row['description'],
			'url' => $query->row['url'],
			'auth_user' => $query->row['auth_user'],
			'auth_password' => $query->row['auth_password'],
			'headers' => json_decode($query->row['headers'], true),
			'actions' => json_decode($query->row['actions'], true),
			'status' => $query->row['status'],
			'date_added' => $query->row['date_added'],
			'date_modified' => $query->row['date_modified'],
		);
	}

	public function getHooks(array $data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "webhook_client` wc";

		$sort_data = array(
			'wc.description',
			'wc.url',
			'wc.actions',
			'wc.date_added',
			'wc.date_modified',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY wc.description";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		$result = array();

		foreach ($query->rows as $row) {
			$result[] = array(
				'webhook_client_id' => $row['webhook_client_id'],
				'description' => $row['description'],
				'url' => $row['url'],
				'auth_user' => $row['auth_user'],
				'auth_password' => $row['auth_password'],
				'headers' => json_decode($row['headers'], true),
				'actions' => json_decode($row['actions'], true),
				'status' => $row['status'],
				'date_added' => $row['date_added'],
				'date_modified' => $row['date_modified'],
			);
		}

		return $result;
	}

	public function getHooksTotal() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "webhook_client`");

		return $query->row['total'];
	}

	public function getRequestHistory($webhook_client_id, array $data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "webhook_request_history` WHERE `webhook_client_id` = '" . (int)$webhook_client_id . "' ORDER BY webhook_request_history_id DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getRequestHistoryTotal($webhook_client_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "webhook_request_history` WHERE `webhook_client_id` = '" . (int)$webhook_client_id . "'");

		return $query->row['total'];
	}

	public function getRequestHistoryInfo($webhook_request_history_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "webhook_request_history` WHERE `webhook_request_history_id` = '" . (int)$webhook_request_history_id . "'");

		return $query->row;
	}

	public function toggleHook($webhook_client_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "webhook_client` SET `status` = IF(`status` = 1, 0, 1) WHERE `webhook_client_id` = '" . (int)$webhook_client_id . "'");
	}
}
