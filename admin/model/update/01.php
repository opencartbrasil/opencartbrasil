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

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "user_login` (
				`user_login_id` int(11) NOT NULL AUTO_INCREMENT,
				`username` varchar(20) NOT NULL,
				`ip` varchar(40) NOT NULL,
				`total` int(4) NOT NULL,
				`date_added` datetime NOT NULL,
				`date_modified` datetime NOT NULL,
				PRIMARY KEY (`user_login_id`),
				KEY `username` (`username`),
				KEY `ip` (`ip`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "api_key` (
				`api_key_id` INT(11) AUTO_INCREMENT,
				`description` TEXT NULL,
				`permissions` VARCHAR(255) NOT NULL,
				`consumer_key` VARCHAR(67) NOT NULL,
				`consumer_secret` VARCHAR(67) NOT NULL,
				`status` TINYINT(1) DEFAULT 0,
				`date_added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`date_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`api_key_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "api_history` (
				`api_history_id` INT(11) AUTO_INCREMENT,
				`api_key_id` INT(11) NOT NULL,
				`type` VARCHAR(64) NULL,
				`date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
				`ip_address` VARCHAR(45) NOT NULL,
				PRIMARY KEY (`api_history_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "api_token` (
				`api_key_id` INT(11) NOT NULL,
				`access_token` TEXT NOT NULL,
				`refresh_token` TEXT NOT NULL,
				`refresh_expire` INT(11) UNSIGNED NOT NULL,
				`status` TINYINT(1) DEFAULT 0,
				PRIMARY KEY (`api_key_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "webhook_client` (
				`webhook_client_id` int(11) NOT NULL AUTO_INCREMENT,
				`description` VARCHAR(255) NOT NULL,
				`url` VARCHAR(255),
				`auth_user` VARCHAR(255),
				`auth_password` VARCHAR(255),
				`headers` TEXT,
				`actions` TEXT,
				`status` TINYINT DEFAULT 0,
				`date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
				`date_modified` DATETIME DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`webhook_client_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "webhook_request_history` (
				`webhook_request_history_id` int(11) NOT NULL AUTO_INCREMENT,
				`webhook_client_id` int(11) NOT NULL,
				`action` VARCHAR(255),
				`request` TEXT,
				`response` TEXT,
				`status_code` TINYINT UNSIGNED,
				`date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`webhook_request_history_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}
}
