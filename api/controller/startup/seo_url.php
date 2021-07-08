<?php
class ControllerStartupSeoUrl extends Controller {
	private $routers = [];

	public function index() {
		$this->routers[] = [
			'path' => '/api/credentials/access_token',
			'action' => 'credentials/token',
			'methods' => ['POST']
		];

		$this->routers[] = [
			'path' => '/api/credentials/refresh_token',
			'action' => 'credentials/token/refresh',
			'methods' => ['POST']
		];

		$this->routers[] = [
			'path' => '/api/product(?:/(?P<product_id>\d+))?',
			'action' => 'product/form',
			'methods' => ['POST', 'PUT']
		];

		$this->routers[] = [
			'path' => '/api/product/(?P<product_id>\d+)',
			'action' => 'product/info',
		];

		$this->routers[] = [
			'path' => '/api/product',
			'action' => 'product/list',
		];

		$this->routers[] = [
			'path' => '/api/product/(?P<product_id>\d+)',
			'action' => 'product/delete',
			'methods' => ['DELETE']
		];

		$this->routers[] = [
			'path' => '/api/language',
			'action' => 'language/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => '/api/stock_status',
			'action' => 'stock_status/list',
			'methods' => ['GET']
		];

		$this->routers[] = [
			'path' => '/api/weight',
			'action' => 'weight/list',
			'methods' => ['GET']
		];
		return $this->start();
	}

	private function start() {
		$parsed_url = parse_url($_SERVER['REQUEST_URI']);

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

			if (isset($parsed_url['path']) && !empty($parsed_url['path'])) {
				$path = $parsed_url['path'];
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
	 * Filtra as rotas com base no método
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
	 * Filtra as rotas com base no caminho da URL
	 *
	 * @param array $routers
	 *
	 * @return array
	 */
	public function filterRoutersByPath($parsed_url, array $routers = []) {
		return array_filter($routers, function($route) use ($parsed_url) {
			return preg_match('~^' . $route['path'] . '$~i', $parsed_url['path']);
		});
	}
}
