<?php
class ControllerCronCurrency extends Controller {
	public function index($data) {
		if ($this->config->get('config_currency_auto')) {
			$this->load->controller('extension/currency/' . $this->config->get('config_currency_engine') . '/currency', $this->config->get('config_currency'));
		}
	}
}