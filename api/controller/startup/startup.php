<?php
class ControllerStartupStartup extends Controller {
	public function index() {
		// Store
		if ($this->request->server['HTTPS']) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store` WHERE REPLACE(`ssl`, 'www.', '') = '" . $this->db->escape('https://' . str_replace('www.', '', $this->request->server['HTTP_HOST']) . rtrim(dirname($this->request->server['PHP_SELF']), '/.\\') . '/') . "'");
		} else {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store` WHERE REPLACE(`url`, 'www.', '') = '" . $this->db->escape('http://' . str_replace('www.', '', $this->request->server['HTTP_HOST']) . rtrim(dirname($this->request->server['PHP_SELF']), '/.\\') . '/') . "'");
		}

		if (isset($this->request->get['store_id'])) {
			$this->config->set('config_store_id', (int)$this->request->get['store_id']);
		} elseif ($query->num_rows) {
			$this->config->set('config_store_id', $query->row['store_id']);
		} else {
			$this->config->set('config_store_id', 0);
		}

		if (!$query->num_rows) {
			$this->config->set('config_url', HTTP_CATALOG);
			$this->config->set('config_ssl', HTTPS_CATALOG);
		}

		// Settings
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY store_id ASC");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$this->config->set($result['key'], $result['value']);
			} else {
				$this->config->set($result['key'], json_decode($result['value'], true));
			}
		}

		// Url
		$this->registry->set('url', new Url($this->config->get('config_url'), $this->config->get('config_ssl')));

		// Currency
		$this->load->model('localisation/currency');
		$currencies = $this->model_localisation_currency->getCurrencies();
		$this->registry->set('currencies', $currencies);

		// Encryption
		$this->registry->set('encryption', new Encryption($this->config->get('config_encryption')));

		// Language
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$this->config->set('languages', $languages);

		$this->response->addHeader('Content-Type: application/json');
	}
}
