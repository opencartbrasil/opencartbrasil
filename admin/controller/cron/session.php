<?php
class ControllerCronSession extends Controller {
	public function index($cron_id, $code, $cycle, $date_added, $date_modified) {
		$this->load->model('cron/session');

		$this->model_cron_session->deleteExpires();
	}
}