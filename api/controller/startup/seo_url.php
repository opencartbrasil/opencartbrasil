<?php
class ControllerStartupSeoUrl extends Controller {
	public function index() {
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			if ($parts[0] == 'product') {
				if ($parts[1] == '') {
					$this->request->get['route'] = 'product/list';
				} elseif ($parts[1] == 'create') {
					$this->request->get['route'] = 'product/create';
				} elseif ($parts[1] == 'update') {
					$this->request->get['route'] = 'product/update';
				} elseif (is_numeric($parts[1])) {
					$this->request->get['route'] = 'product/info';
					$this->request->get['product_id'] = intval($parts[1]);
				} else {
					$this->request->get['route'] = 'error/not_found';
				}
			} else {
				$this->request->get['route'] = 'error/not_found';
			}
		}
	}
}
