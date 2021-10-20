<?php
class ModelUpgrade1010 extends Model {
	public function upgrade() {
		// Table api_key
		$query = $this->db->query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "api_key'");

		if ($query->num_rows == 0) {
			$sql = "
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
			";

			$this->db->query($sql);
		}

		// Table api_history
		$query = $this->db->query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "api_history'");

		if ($query->num_rows == 0) {
			$sql = "
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "api_history` (
					`api_history_id` INT(11) AUTO_INCREMENT,
					`api_key_id` INT(11) NOT NULL,
					`type` VARCHAR(64) NULL,
					`date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
					`ip_address` VARCHAR(45) NOT NULL,
					PRIMARY KEY (`api_history_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
			";

			$this->db->query($sql);
		}

		// Table api_token
		$query = $this->db->query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "api_token'");

		if ($query->num_rows == 0) {
			$sql = "
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "api_token` (
					`api_key_id` INT(11) NOT NULL,
					`access_token` TEXT NOT NULL,
					`refresh_token` TEXT NOT NULL,
					`refresh_expire` INT(11) UNSIGNED NOT NULL,
					`status` TINYINT(1) DEFAULT 0,
					PRIMARY KEY (`api_key_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
			";

			$this->db->query($sql);
		}

		// Table webhook_client
		$query = $this->db->query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "webhook_client'");

		if ($query->num_rows == 0) {
			$sql = "
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
			";

			$this->db->query($sql);
		}

		// Table webhook_request_history
		$query = $this->db->query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "webhook_request_history'");

		if ($query->num_rows == 0) {
			$sql = "
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
			";

			$this->db->query($sql);
		}

		// Column `code` in table custom_field
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "custom_field' AND COLUMN_NAME = 'code'");

		if ($query->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "custom_field` ADD COLUMN `code` VARCHAR(255) AFTER `type`");
		}

		// Create API config.php
		$file_api_config = DIR_OPENCART . "api/config.php";

		if (!file_exists($file_api_config)) {
			$lines = array();

			$lines[] = "<?php\n";
			$lines[] = "// HTTP\n";
			$lines[] = "define('HTTP_SERVER', '" . HTTP_SERVER . "api');\n";
			$lines[] = "define('HTTP_CATALOG', '" . HTTP_SERVER . "');\n\n";

			$lines[] = "// HTTPS\n";
			$lines[] = "define('HTTPS_SERVER', '" . HTTP_SERVER . "api');\n";
			$lines[] = "define('HTTPS_CATALOG', '" . HTTP_SERVER . "');\n\n";

			$lines[] = "// DIR\n";
			$lines[] = "define('DIR_APPLICATION', '" . addslashes(DIR_OPENCART) ."api/');\n";
			$lines[] = "define('DIR_SYSTEM', '" . addslashes(DIR_OPENCART) ."system/');\n";
			$lines[] = "define('DIR_IMAGE', '" . addslashes(DIR_OPENCART) ."image/');\n";
			$lines[] = "define('DIR_WEBHOOK', '" . addslashes(DIR_OPENCART) ."webhook/');\n";
			$lines[] = "define('DIR_STORAGE', DIR_SYSTEM . 'storage/');\n";
			$lines[] = "define('DIR_CONFIG', DIR_SYSTEM . 'config/');\n";
			$lines[] = "define('DIR_LOGS', DIR_STORAGE . 'logs/');\n";
			$lines[] = "define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');\n";
			$lines[] = "define('DIR_CACHE', DIR_STORAGE . 'cache/');\n";
			$lines[] = "define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');\n";
			$lines[] = "define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');\n\n";

			$lines[] = "// DB\n";
			$lines[] = "define('DB_DRIVER', '" . DB_DRIVER . "');\n";
			$lines[] = "define('DB_HOSTNAME', '" . DB_HOSTNAME . "');\n";
			$lines[] = "define('DB_USERNAME', '" . DB_USERNAME . "');\n";
			$lines[] = "define('DB_PASSWORD', '" . DB_PASSWORD . "');\n";
			$lines[] = "define('DB_DATABASE', '" . DB_DATABASE . "');\n";
			$lines[] = "define('DB_PORT', '" . DB_PORT . "');\n";
			$lines[] = "define('DB_PREFIX', '" . DB_PREFIX . "');\n";

			$handler = fopen($file_api_config, 'w');
			fwrite($handler, implode('', $lines));
			fclose($handler);
		}

		// Update config.php
		$files = glob(DIR_OPENCART . '{config.php,admin/config.php}', GLOB_BRACE);

		foreach ($files as $file) {
			$lines = file($file);

			for ($i = 0; $i < count($lines); $i++) {
				if ((strpos($lines[$i], 'DIR_IMAGE') !== false) && (strpos($lines[$i + 1], 'DIR_WEBHOOK') === false)) {
					array_splice($lines, $i + 1, 0, array("define('DIR_WEBHOOK', '" . addslashes(DIR_OPENCART) . "webhook/');\n"));
				}
			}

			$output = implode('', $lines);

			$handle = fopen($file, 'w');

			fwrite($handle, $output);

			fclose($handle);
		}
	}
}
