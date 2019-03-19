<?php
class ControllerCronSession extends Controller {
	public function index($data) {
		$this->load->model('cron/session');

		$this->model_cron_session->deleteExpires();
	}
}