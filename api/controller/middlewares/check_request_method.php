<?php
class ControllerMiddlewaresCheckRequestMethod extends Controller {
	public function before() {
		if (in_array($this->request->get['route'], $this->config->get('ignored_routers'))) {
			return;
		}

		$scopes = explode(',', $this->jwt->scope);

		if (!in_array($this->request->server['REQUEST_METHOD'], $scopes)) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'without_permission_for_the_method',
						'message' => 'You are not allowed to use the method "' . $this->request->server['REQUEST_METHOD'] . '".'
					)
				)
			)));

			return new Action('status_code/unauthorized');
		}
	}
}
