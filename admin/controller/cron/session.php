<?php
class ControllerCronSession extends Controller {
	public function index($data) {
		$this->load->model('cron/session');

		$this->model_cron_session->deleteExpires();

		if (ini_get('session.gc_maxlifetime')) {
			$gc_maxlifetime = ini_get('session.gc_maxlifetime');
		} else {
			$gc_maxlifetime = 3600;
		}

		$expire = time() - $gc_maxlifetime;

		$files = glob(DIR_SESSION . 'sess_*');

		foreach ($files as $file) {
			if (filemtime($file) < $expire) {
				unlink($file);
			}
		}
	}
}