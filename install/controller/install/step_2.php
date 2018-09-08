<?php
class ControllerInstallStep2 extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('install/step_2');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->response->redirect($this->url->link('install/step_3'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action'] = $this->url->link('install/step_2');

		$data['php_version'] = phpversion();

		if (version_compare(phpversion(), '5.6.0', '<')) {
			$data['version'] = false;
		} else {
			$data['version'] = true;
		}

		$data['register_globals'] = ini_get('register_globals');
		$data['magic_quotes_gpc'] = ini_get('magic_quotes_gpc');
		$data['file_uploads'] = ini_get('file_uploads');
		$data['session_auto_start'] = ini_get('session_auto_start');

		$db = array(
			'mysqli',
			'pgsql',
			'pdo'
		);

		if (!array_filter($db, 'extension_loaded')) {
			$data['db'] = false;
		} else {
			$data['db'] = true;
		}

		$data['gd'] = extension_loaded('gd');
		$data['curl'] = extension_loaded('curl');
		$data['openssl'] = function_exists('openssl_encrypt');
		$data['zlib'] = extension_loaded('zlib');
		$data['zip'] = extension_loaded('zip');
		$data['iconv'] = function_exists('iconv');
		$data['mbstring'] = extension_loaded('mbstring');

		$data['catalog_config'] = DIR_OPENCART . 'config.php';
		$data['admin_config'] = DIR_OPENCART . 'admin/config.php';

		// catalog config
		if (!is_file(DIR_OPENCART . 'config.php')) {
			$data['error_catalog_config'] = $this->language->get('text_missing');
		} elseif (!is_writable(DIR_OPENCART . 'config.php')) {
			$data['error_catalog_config'] = $this->language->get('text_unwritable');
		} else {
			$data['error_catalog_config'] = '';
		}

		// admin configs
		if (!is_file(DIR_OPENCART . 'admin/config.php')) {
			$data['error_admin_config'] = $this->language->get('text_missing');
		} elseif (!is_writable(DIR_OPENCART . 'admin/config.php')) {
			$data['error_admin_config'] = $this->language->get('text_unwritable');
		} else {
			$data['error_admin_config'] = '';
		}

		$data['catalog_config'] = DIR_OPENCART . 'config.php';
		$data['admin_config'] = DIR_OPENCART . 'admin/config.php';

		$data['back'] = $this->url->link('install/step_1');

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('install/step_2', $data));
	}

	private function validate() {
		if (version_compare(phpversion(), '5.6.0', '<')) {
			$this->error['warning'] = $this->language->get('error_version');
		}

		if (!ini_get('file_uploads')) {
			$this->error['warning'] = $this->language->get('error_file_upload');
		}

		if (ini_get('session.auto_start')) {
			$this->error['warning'] = $this->language->get('error_session');
		}

		$db = array(
			'mysql',
			'mysqli',
			'pdo',
			'pgsql'
		);

		if (!array_filter($db, 'extension_loaded')) {
			$this->error['warning'] = $this->language->get('error_db');
		}

		if (!extension_loaded('gd')) {
			$this->error['warning'] = $this->language->get('error_gd');
		}

		if (!extension_loaded('curl')) {
			$this->error['warning'] = $this->language->get('error_curl');
		}

		if (!function_exists('openssl_encrypt')) {
			$this->error['warning'] = $this->language->get('error_openssl');
		}

		if (!extension_loaded('zlib')) {
			$this->error['warning'] = $this->language->get('error_zlib');
		}

		if (!extension_loaded('zip')) {
			$this->error['warning'] = $this->language->get('error_zip');
		}

		if (!function_exists('iconv') && !extension_loaded('mbstring')) {
			$this->error['warning'] = $this->language->get('error_mbstring');
		}

		if (!is_file(DIR_OPENCART . 'config.php')) {
			$this->error['warning'] = $this->language->get('error_catalog_exist');
		} elseif (!is_writable(DIR_OPENCART . 'config.php')) {
			$this->error['warning'] = $this->language->get('error_catalog_writable');
		} elseif (!is_file(DIR_OPENCART . 'admin/config.php')) {
			$this->error['warning'] = $this->language->get('error_admin_exist');
		} elseif (!is_writable(DIR_OPENCART . 'admin/config.php')) {
			$this->error['warning'] = $this->language->get('error_admin_writable');
		}

		return !$this->error;
	}
}
