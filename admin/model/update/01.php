<?php
class ModelUpdate01 extends Model {
	public function update() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cron` (
				`cron_id` int(11) NOT NULL AUTO_INCREMENT,
				`code` varchar(64) NOT NULL,
				`cycle` varchar(12) NOT NULL,
				`action` text NOT NULL,
				`status` tinyint(1) NOT NULL,
				`date_added` datetime NOT NULL,
				`date_modified` datetime NOT NULL,
				PRIMARY KEY (`cron_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "customer_affiliate_report` (
				`customer_affiliate_report_id` int(11) NOT NULL AUTO_INCREMENT,
				`customer_id` int(11) NOT NULL,
				`store_id` int(11) NOT NULL,
				`ip` varchar(40) NOT NULL,
				`country` varchar(2) NOT NULL,
				`date_added` datetime NOT NULL,
				PRIMARY KEY (`customer_affiliate_report_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "download_report` (
				`download_report_id` int(11) NOT NULL AUTO_INCREMENT,
				`download_id` int(11) NOT NULL,
				`store_id` int(11) NOT NULL,
				`ip` varchar(40) NOT NULL,
				`country` varchar(2) NOT NULL,
				`date_added` datetime NOT NULL,
				PRIMARY KEY (`download_report_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "marketing_report` (
				`marketing_report_id` int(11) NOT NULL AUTO_INCREMENT,
				`marketing_id` int(11) NOT NULL,
				`store_id` int(11) NOT NULL,
				`ip` varchar(40) NOT NULL,
				`country` varchar(2) NOT NULL,
				`date_added` datetime NOT NULL,
				PRIMARY KEY (`marketing_report_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}
}