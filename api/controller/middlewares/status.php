<?php
class ControllerMiddlewaresStatus extends Controller {
	public function before() {
		if (!$this->config->get('config_api_rest')) {
			return new Action('status_code/service_unavailable');
		}
	}
}
