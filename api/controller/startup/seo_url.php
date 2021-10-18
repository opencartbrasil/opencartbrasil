<?php
class ControllerStartupSeoUrl extends Controller {
	private $routers = array();

	public function index() {
		$this->routers[] = array(
			'path' => 'documentation',
			'action' => 'common/documentation'
		);

		$this->routers[] = array(
			'path' => 'access_token',
			'action' => 'credentials/access_token',
			'methods' => array('POST')
		);

		$this->routers[] = array(
			'path' => 'refresh_token',
			'action' => 'credentials/refresh_token',
			'methods' => array('POST')
		);

		$this->routers[] = array(
			'path' => 'product(?:/(?P<product_id>\d+))?',
			'action' => 'product/form',
			'methods' => array('POST', 'PUT')
		);

		$this->routers[] = array(
			'path' => 'product/(?P<product_id>\d+)',
			'action' => 'product/info',
		);

		$this->routers[] = array(
			'path' => 'product',
			'action' => 'product/list',
		);

		$this->routers[] = array(
			'path' => 'product/(?P<product_id>\d+)',
			'action' => 'product/delete',
			'methods' => array('DELETE')
		);

		$this->routers[] = array(
			'path' => 'product/stock',
			'action' => 'product/form_stock',
			'methods' => array('PUT')
		);

		$this->routers[] = array(
			'path' => 'language',
			'action' => 'language/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'tax_class',
			'action' => 'tax_class/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'stock_status',
			'action' => 'stock_status/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'length',
			'action' => 'length/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'weight',
			'action' => 'weight/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'category',
			'action' => 'category/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'filter_group',
			'action' => 'filter/group_list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'attribute_group',
			'action' => 'attribute/attribute_group_list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'attribute',
			'action' => 'attribute/attribute_list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'manufacturer',
			'action' => 'manufacturer/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'download',
			'action' => 'download/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'option',
			'action' => 'option/list',
			'methods' => array('GET')
		);

		$this->routers[] = array(
			'path' => 'order',
			'action' => 'order/list',
		);

		$this->routers[] = array(
			'path' => 'order/(?P<order_id>\d+)',
			'action' => 'order/info',
		);

		$this->routers[] = array(
			'path' => 'order/history',
			'action' => 'order/form_history',
			'methods' => array('PUT')
		);

		$this->routers[] = array(
			'path' => 'order_status',
			'action' => 'order_status/list',
		);

		return $this->start();
	}

	private function start() {
		if (isset($this->request->get['_route_'])) {
			$parsed_url = $this->request->get['_route_'];
		} elseif (isset($this->request->get['route'])) {
			$parsed_url = $this->request->get['route'];
		} else {
			$parsed_url = null;
		}

		if ($parsed_url == '/' || empty($parsed_url)) {
			return new Action('status_code/unauthorized');
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

			$regex  = '~^(' . $route['path'] . '|' . $route['action'] . ')$~i';

			$matches = array();

			$isValid = preg_match($regex, $path, $matches);

			if (!$isValid) {
				continue;
			}

			array_shift($matches);

			if (!isset($route['methods'])) {
				$route['methods'] = array('GET');
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
	public function filterRoutersByMethod(string $requestMethod, array $routers = array()) {
		return array_filter($routers, function($route) use ($requestMethod) {
			if (!isset($route['methods'])) {
				$route['methods'] = array('GET');
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
			return preg_match('~^' . $route['path'] . '$~i', $parsed_url) || preg_match('~^' . $route['action'] . '$~i', $parsed_url);
		});
	}
}
