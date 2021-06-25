<?php

class ControllerStartupPermission extends Controller {
	public function index() {
		$ignoredRoutes = [
			'credentials/token',
		];

		if (in_array($this->request->get['route'], $ignoredRoutes)) {
			return;
		}

		$this->load->model('credentials/permission');

		$has_permission = $this->model_credentials_permission->hasPermissionForCurrentRoute();

		if ($has_permission === false) {
			$this->response->setOutput(json_encode([
				'success' => false,
				'errors' => [
					'code' => 'invalid_permission',
					'message' => 'You don\'t have permission'
				]
			]));
			return new Action('status_code/forbidden');
		}
	}
}
