<?php

class ModelAdvancedWebHook extends Model {
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

		return $query->rows;
	}

	public function getHooksTotal() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "webhook_client`");

		return $query->row['total'];
	}
}
