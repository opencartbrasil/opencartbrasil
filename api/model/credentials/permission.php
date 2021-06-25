<?php

class ModelCredentialsPermission extends Model {
	public function hasPermissionForCurrentRoute() {
		$route = $this->request->get['route'];
		$method = $this->request->server['REQUEST_METHOD'];

		return $route === 'product/info' && $method === 'GET';
		//return true;
	}
}
