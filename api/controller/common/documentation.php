<?php
class ControllerCommonDocumentation extends Controller {
	public function index() {
		if ($this->config->get('config_api_status')) {
			return new Action('common/documentation');
		}

		$this->response->addHeader('Content-Type: text/html');
		$this->response->setOutput($this->load->view('common/documentation'));
	}
}
