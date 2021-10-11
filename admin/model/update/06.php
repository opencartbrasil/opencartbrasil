<?php
class ModelUpdate06 extends Model {
	public function update() {
		$dir_opencart = str_replace("admin/", "", DIR_APPLICATION);

		// Files config.php
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
