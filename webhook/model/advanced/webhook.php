<?php
class ModelWebhookAdvancedWebhook extends Model {
	public function getHooks($action) {
		$query = $this->db->query("SELECT `webhook_client_id`, `url`, CONCAT(`auth_user`, ':', `auth_password`) AS `auth`, `headers` FROM `" . DB_PREFIX . "webhook_client` WHERE `actions` LIKE '%" . $this->db->escape($action) . "%' AND `status` = 1");

		$result = array();

		foreach ($query->rows as $row) {
			$result[] = array(
				'webhook_client_id' => $row['webhook_client_id'],
				'url' => $row['url'],
				'auth' => $row['auth'],
				'headers' => json_decode($row['headers'], true),
			);
		}

		return $result;
	}

	public function saveRequest($webhook_client_id, $action, $request, $response, $status_code) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "webhook_request_history` SET `webhook_client_id` = '" . (int)$webhook_client_id . "', `action` = '" . $this->db->escape($action) . "', `request` = '" . $this->db->escape(json_encode($request)) . "', `response` = '" . $this->db->escape($response) . "', `status_code` = '" . (int)$status_code . "'");
	}
}
