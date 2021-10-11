<?php
class ModelUpdate06 extends Model {
	public function update() {
		$dir_opencart = str_replace("admin/", "", DIR_APPLICATION);

		// Create API config.php
		$file_api_config = $dir_opencart . "api/config.php";

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
			$lines[] = "define('DIR_APPLICATION', '" . addslashes($dir_opencart) ."api/');\n";
			$lines[] = "define('DIR_WEBHOOK', '" . addslashes($dir_opencart) ."webhook/');\n";
			$lines[] = "define('DIR_SYSTEM', '" . addslashes($dir_opencart) ."system/');\n";
			$lines[] = "define('DIR_IMAGE', '" . addslashes($dir_opencart) ."image/');\n";
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
		$files = glob($dir_opencart . '{config.php,admin/config.php}', GLOB_BRACE);

		foreach ($files as $file) {
			$lines = file($file);

			for ($i = 0; $i < count($lines); $i++) {
				if ((strpos($lines[$i], 'DIR_IMAGE') !== false) && (strpos($lines[$i + 1], 'DIR_WEBHOOK') === false)) {
					array_splice($lines, $i + 1, 0, array("define('DIR_WEBHOOK', '" . addslashes($dir_opencart) . "webhook/');\n"));
				}
			}

			$output = implode('', $lines);

			$handle = fopen($file, 'w');

			fwrite($handle, $output);

			fclose($handle);
		}
	}
}
