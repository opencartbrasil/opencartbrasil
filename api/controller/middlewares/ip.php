<?php
class ControllerMiddlewaresIp extends Controller {
	public function before() {
		if (in_array($this->request->get['route'], $this->config->get('ignored_routers'))) {
			return;
		}

		$this->load->model('middlewares/ip');

		$this->config->load('api/settings/ip_blocked');

		$ip = $this->request->server['REMOTE_ADDR'];

		$has_blocked = in_array($ip, $this->config->get('ip_blocked'));

		if ($has_blocked) {
			$this->log->write(array(
				'failed' => "IP \"{$ip}\" is blocked."
			));

			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'no_permission',
						'message' => 'You do not have permission.'
					)
				)
			)));

			return new Action('status_code/forbidden');
		}
	}
}
