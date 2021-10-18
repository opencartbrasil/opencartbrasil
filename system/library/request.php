<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Request class
*/
class Request {
	public $get = array();
	public $post = array();
	public $cookie = array();
	public $files = array();
	public $server = array();
	public $headers = array();
	public $json = array();

	/**
	 * Constructor
	*/
	public function __construct() {
		$this->get = $this->clean($_GET);
		$this->post = $this->clean($_POST);
		$this->request = $this->clean($_REQUEST);
		$this->cookie = $this->clean($_COOKIE);
		$this->files = $this->clean($_FILES);
		$this->server = $this->getServer();

		$json = json_decode(file_get_contents('php://input'), true);

		if (json_last_error() == JSON_ERROR_NONE) {
			$json = array_map(array($this, 'clean'), $json);
			$json = json_encode($json);

			$this->json = json_decode($json);
		}

		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			$headers_keys = array_map('strtolower', array_keys($headers));
			$headers_values = array_map(array($this, 'clean'), array_values($headers));

			$this->headers = array_combine($headers_keys, array_map('trim', $headers_values));
		}
	}

	/**
	 *
	 * @param	array	$data
	 *
	 * @return	array
	 */
	public function clean($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);

				$data[$this->clean($key)] = $this->clean($value);
			}
		} elseif (!is_double($data) && !is_int($data) && !is_float($data) && !is_bool($data)) {
			$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
		}

		return $data;
	}

	private function getServer() {
		$_SERVER["REMOTE_ADDR"] = $this->getRealRemoteAddr();

		return $this->clean($_SERVER);
	}

	private function getRealRemoteAddr() {
		$ip = '';

		if (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$xip = trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));

			if (filter_var($xip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != $xip) {
					$ip = $xip;
				}
			}
		}

		// Cloudflare
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		// Incapsula
		if (isset($_SERVER['HTTP_INCAP_CLIENT_IP']) && filter_var($_SERVER['HTTP_INCAP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
			$ip = $_SERVER['HTTP_INCAP_CLIENT_IP'];
		}

		// Sucuri
		if (isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']) && filter_var($_SERVER['HTTP_X_SUCURI_CLIENTIP'], FILTER_VALIDATE_IP)) {
			$ip = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
		}

		return $ip;
	}
}
