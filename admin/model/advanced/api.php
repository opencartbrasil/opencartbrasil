<?php

class ModelAdvancedApi extends Model {
	public function addApi($data) {
		$permissions = join(",", $data["permissions"]);

		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_key` SET `description` = '" . $this->db->escape($data["description"]) . "', `permissions` = '" . $this->db->escape($permissions) . "', `consumer_key` = '" . $this->db->escape($data["consumer_key"]) . "', `consumer_secret` = '" . $this->db->escape($data["consumer_secret"]) . "', `status` = '" . (int)$data["status"] . "'");
	}

	public function editApi($api_key_id, $data) {
		$permissions = join(",", $data["permissions"]);

		$this->db->query("UPDATE `" . DB_PREFIX . "api_key` SET `description` = '" . $this->db->escape($data["description"]) . "', `permissions` = '" . $this->db->escape($permissions) . "', `status` = '" . (int)$data["status"] . "', `date_modified` = NOW() WHERE `api_key_id` = '" . (int)$api_key_id . "'");
	}

	public function deleteApi($api_key_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api_key` WHERE `api_key_id` = '" . (int)$api_key_id . "'");
	}

	public function toggleStatusApi($api_key_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "api_key` SET `status` = IF(`status` = 1, 0, 1) WHERE `api_key_id` = '" . (int)$api_key_id . "'");
	}

	public function getApis($data = array()) {
		$sql = "SELECT a.*, (SELECT ahi.date_added FROM `" . DB_PREFIX . "api_history` ahi WHERE ahi.api_key_id = ah.api_key_id ORDER BY ahi.date_added DESC LIMIT 1) AS last_access FROM `" . DB_PREFIX . "api_key` a LEFT JOIN `" . DB_PREFIX . "api_history` ah ON (ah.api_key_id = a.api_key_id) GROUP BY ah.api_key_id";

		$sort_data = array(
			'a.description',
			'a.status',
			'a.date_added',
			'a.date_modified',
			'ah.last_access',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.description";
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

		return $query->rows;
	}

	public function getApi($api_key_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_key` WHERE `api_key_id` = '" . (int)$api_key_id . "'");

		return $query->row;
	}

	public function getApisTotal() {
		$sql = "SELECT api_key_id FROM `" . DB_PREFIX . "api_key`";

		$query = $this->db->query($sql);

		return $query->num_rows;
	}

	public function getApiHistories($api_key_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_history` WHERE `api_key_id` = '" . (int)$api_key_id . "' ORDER BY `date_added` DESC");

		return $query->rows;
	}

	public function consumerKeysExists($data) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "api_key` WHERE `consumer_key` = '" . $this->db->escape($data['consumer_key']) . "' OR `consumer_secret` = '" . $this->db->escape($data['consumer_secret']) . "'");

		return !!$query->row['total'];
	}
}
