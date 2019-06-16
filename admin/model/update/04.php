<?php
class ModelUpdate04 extends Model {
	public function update() {
		$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `trigger` = 'catalog/model/account/affiliate/addAffiliate/after' WHERE event_id = 10;");

		$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `trigger` = 'catalog/model/account/affiliate/editAffiliate/after' WHERE event_id = 11;");

		$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `trigger` = 'catalog/model/account/affiliate/addAffiliate/after' WHERE event_id = 18;");

		$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `trigger` = 'catalog/model/account/affiliate/addAffiliate/after' WHERE event_id = 19;");

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE code = 'statistics_order_delete'");
		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` (`code`, `trigger`, `action`, `status`) VALUES ('statistics_order_delete', 'catalog/model/checkout/order/deleteOrder/before', 'event/statistics/deleteOrder', 1);");
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE code = 'admin_statistics_review_delete'");
		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` (`code`, `trigger`, `action`, `status`) VALUES ('admin_statistics_review_delete', 'admin/model/catalog/review/deleteReview/before', 'event/statistics/deleteReview', 1);");
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE code = 'admin_statistics_return_delete'");
		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` (`code`, `trigger`, `action`, `status`) VALUES ('admin_statistics_return_delete', 'admin/model/sale/return/deleteReturn/before', 'event/statistics/deleteReturn', 1);");
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE code = 'admin_statistics_review_add'");
		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` (`code`, `trigger`, `action`, `status`) VALUES ('admin_statistics_review_add', 'admin/model/catalog/review/addReview/after', 'event/statistics/addReview', 1);");
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE code = 'admin_statistics_return_add'");
		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` (`code`, `trigger`, `action`, `status`) VALUES ('admin_statistics_return_add', 'admin/model/sale/return/addReturn/after', 'event/statistics/addReturn', 1);");
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "cron` WHERE code = 'session'");
		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "cron` (`cron_id`, `code`, `cycle`, `action`, `status`, `date_added`, `date_modified`) VALUES (3, 'session', 'day', 'cron/session', 1, '2019-03-19 04:33:00', '2019-03-19 04:33:00');");
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "cron` WHERE code = 'currency'");
		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "cron` (`cron_id`, `code`, `cycle`, `action`, `status`, `date_added`, `date_modified`) VALUES (1, 'currency', 'day', 'cron/currency', 1, '2019-06-16 01:19:00', '2019-06-16 01:19:00');");
		}
	}
}