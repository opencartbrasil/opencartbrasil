<?php
class ControllerStartupSeoUrl extends Controller {
	private $routers = [];

	public function index() {
		$this->routers[] = [
			'path' => 'documentation',
			'action' => 'common/documentation'
		];

		$this->routers[] = [
			'path' => 'credentials/access_token',
			'action' => 'credentials/token',
			'methods' => ['POST']
		];

		$this->routers[] = [
			'path' => 'credentials/refresh_token',
			'action' => 'credentials/refresh_token',
			'methods' => ['POST']
		];

		$this->routers[] = [
			'path' => 'product(?:/(?P<product_id>\d+))?',
			'action' => 'product/form',
			'methods' => ['POST', 'PUT']
		];

		$this->routers[] = [
			'path' => 'product/(?P<product_id>\d+)',
			'action' => 'product/info',
		];

		$this->routers[] = [
			'path' => 'product',
			'action' => 'product/list',
		];

		$this->routers[] = [
			'path' => 'product/(?P<product_id>\d+)',
			'action' => 'product/delete',
			'methods' => ['DELETE']
		];

		$this->routers[] = [
			'path' => 'product/stocks',
			'action' => 'product/form_stock',
			'methods' => ['PUT']
		];

		$this->routers[] = [
			'path' => 'language',
			'action' => 'language/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'tax_class',
			'action' => 'tax_class/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'stock_status',
			'action' => 'stock_status/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'length',
			'action' => 'length/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'weight',
			'action' => 'weight/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'category',
			'action' => 'category/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'filter_group',
			'action' => 'filter/group_list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'attribute_group',
			'action' => 'attribute/attribute_group_list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'attribute',
			'action' => 'attribute/attribute_list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'manufacturer',
			'action' => 'manufacturer/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'download',
			'action' => 'download/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'option',
			'action' => 'option/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => 'orders',
			'action' => 'order/list',
		];

		$this->routers[] = [
			'path' => 'order/(?P<order_id>\d+)',
			'action' => 'order/info',
		];

		$this->routers[] = [
			'path' => 'orders/history',
			'action' => 'order/form_history',
			'methods' => ['PUT']
		];

		return $this->start();
	}

	private function start() {
		if (isset($this->request->get['_route_'])) {
			$parsed_url = $this->request->get['_route_'];
		} else {
			$parsed_url = $this->request->get['route'];
		}

		$requestMethod = $_SERVER['REQUEST_METHOD'];
		$path_default = '/';
		$pathMatchFound = false;

		$routers = $this->filterRoutersByMethod($requestMethod, $this->routers);

		if (empty($routers)) {
			return new Action('status_code/method_not_allowed');
		}

		$routers = $this->filterRoutersByPath($parsed_url, $routers);

		if (empty($routers)) {
			return new Action('status_code/method_not_allowed');
		}

		foreach($routers as $route) {
			$path = $path_default;

			if (empty($route['path'])) {
				continue;
			}

			if (empty($route['action'])) {
				$route['action'] = $this->config->get('action_default');
			}

			if (!empty($parsed_url)) {
				$path = $parsed_url;
			}

			$regex  = '~^' . $route['path'] . '$~i';

			$matches = [];

			$isValid = preg_match($regex, $path, $matches);

			if (!$isValid) {
				continue;
			}

			array_shift($matches);

			if (!isset($route['methods'])) {
				$route['methods'] = ['GET'];
			}

			$allowedMethod = array_map('strtoupper', $route['methods']);

			foreach ($matches as $key => $value) {
				$this->request->get[$key] = $value;
			}

			$this->request->get['route'] = $route['action'];

			$pathMatchFound = true;
		}

		if ($pathMatchFound === false) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					'code' => 'invalid_route',
					'message' => 'The route accessed is invalid.'
				)
			)));

			return new Action('status_code/bad_request');
		}
	}

	/**
	 * Filters routes based on method
	 *
	 * @param array $routers
	 *
	 * @return array
	 */
	public function filterRoutersByMethod(string $requestMethod, array $routers = []) {
		return array_filter($routers, function($route) use ($requestMethod) {
			if (!isset($route['methods'])) {
				$route['methods'] = ['GET'];
			}

			return in_array($requestMethod, $route['methods']);
		});
	}

	/**
	 * Filters routes based on URL path
	 *
	 * @param array $routers
	 *
	 * @return array
	 */
	public function filterRoutersByPath($parsed_url, array $routers = []) {
		return array_filter($routers, function($route) use ($parsed_url) {
			return preg_match('~^' . $route['path'] . '$~i', $parsed_url);
		});
	}
}
