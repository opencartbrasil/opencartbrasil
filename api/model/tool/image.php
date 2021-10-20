<?php
class ModelToolImage extends Model {
	public const ERROR_MIMETYPE = 1000;
	public const ERROR_SIZE = 2000;
	public const ERROR_UNKNOWN = 3000;

	public function download(string $url) {
		if (empty($url)) {
			return 'no_image.png';
		}

		$filename_temporary = tempnam(sys_get_temp_dir(), uniqid());
		$file_temporary = fopen($filename_temporary, 'w+');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FILE, $file_temporary);
		curl_exec($ch);
		curl_close($ch);
		fclose($file_temporary);

		$image_info = @getimagesize($filename_temporary);

		if ($image_info === false) {
			throw new \InvalidArgumentException('Invalid image', self::ERROR_MIMETYPE);
		}

		if (!in_array($image_info['mime'], $this->config->get('image_mimetypes'))) {
			throw new \InvalidArgumentException('Invalid mimetype', self::ERROR_MIMETYPE);
		}

		if (filesize($filename_temporary) > $this->config->get('config_file_max_size')) {
			throw new \InvalidArgumentException('File size is too big', self::ERROR_SIZE);
		}

		$dir_upload = rtrim($this->config->get('image_dir_download'), '/') . '/';

		if (!is_dir($dir_upload)) {
			@mkdir($dir_upload, 0777, true);
		}

		$new_file = $dir_upload . $this->sanitize_file(basename($url));

		while (file_exists($new_file) && !$this->config->get('image_download_overwrite')) {
			$new_file = $dir_upload . $this->sanitize_file(uniqid() . '-' . basename($url));
		};

		$result = copy($filename_temporary, $new_file);

		if (file_exists($filename_temporary)) {
			unlink($filename_temporary);
		}

		if ($result) {
			return str_replace(DIR_IMAGE, '', $new_file);
		}

		throw new \RuntimeException('Unknown Error', self::ERROR_UNKNOWN);
	}

	private function sanitize_file($string) {
		setlocale(LC_ALL, 'en_US.UTF8');

		$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		$string = preg_replace("/[^a-zA-Z0-9-.]/", '', $string);

		return strtolower(trim($string, '-'));
	}
}
