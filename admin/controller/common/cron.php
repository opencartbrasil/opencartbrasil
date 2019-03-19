<?php
class ControllerCommonCron extends Controller {
	public function index() {
		$time = time();

		$this->load->model('setting/cron');

		$results = $this->model_setting_cron->getCrons();

		foreach ($results as $result) {
			if ($result['status'] && (strtotime('+1 ' . $result['cycle'], strtotime($result['date_modified'])) < ($time + 10))) {
				$this->load->controller($result['action'], $result);

				$this->model_setting_cron->editCron($result['cron_id']);
			}
		}
	}
}
