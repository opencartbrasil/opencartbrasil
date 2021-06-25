<?php

class ControllerMiddlewaresIp extends Controller {
	public function before() {
		$ignoredRoutes = [
			'credentials/token',
		];

		if (in_array($this->request->get['route'], $ignoredRoutes)) {
			return;
		}

		$this->load->model('middlewares/ip');

		$ip = $this->request->server['REMOTE_ADDR'];

		$has_blocked = $this->model_middlewares_ip->hasBlocked($ip);

		if ($has_blocked) {
			$this->log->write(array(
				'failed' => "IP \"{$ip}\" is blocked."
			));

			return new Action('status_code/forbidden');
		}
	}
}
