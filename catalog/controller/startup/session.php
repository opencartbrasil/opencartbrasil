<?php
class ControllerStartupSession extends Controller {
	public function index() {
		if (isset($this->request->get['route']) && substr((string)$this->request->get['route'], 0, 4) == 'api/') {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "api_session` WHERE TIMESTAMPADD(HOUR, 1, date_modified) < NOW()");

			$ip = $this->request->server['REMOTE_ADDR'];

			// Make sure the IP is allowed
			$api_token = (array_key_exists('api_token', $this->request->get)) ? (string)$this->request->get['api_token'] : '';

			$api_query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "api` `a` LEFT JOIN `" . DB_PREFIX . "api_session` `as` ON (a.api_id = as.api_id) LEFT JOIN " . DB_PREFIX . "api_ip `ai` ON (a.api_id = ai.api_id) WHERE a.status = '1' AND `as`.`session_id` = '" . $this->db->escape((string)$api_token) . "' AND ai.ip = '" . $this->db->escape((string)$ip) . "'");

			if ($api_query->num_rows) {
				$this->session->start($api_token);

				// keep the session alive
				$this->db->query("UPDATE `" . DB_PREFIX . "api_session` SET `date_modified` = NOW() WHERE `api_session_id` = '" . (int)$api_query->row['api_session_id'] . "'");
			}
		} else {
			$session_id = $this->session->get_cookie();
			$this->session->start($session_id);
			$this->session->set_cookie();
		}
	}
}
