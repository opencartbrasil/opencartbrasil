<?php
class ControllerStartupSeoUrl extends Controller {
	private $routers = [];

	public function index() {
		$this->routers[] = [
			'path' => '/api/product',
			'action' => 'product/create',
			'methods' => [
				'POST'
			]
		];

		$this->start();
	}

	private function start() {
		$parsed_url = parse_url($_SERVER['REQUEST_URI']);

		$requestMethod = $_SERVER['REQUEST_METHOD'];

		$path_default = '/';

		$pathMatchFound = false;

		$routers = array_filter($this->routers, function($route) use ($requestMethod) {
			if (!isset($route['methods'])) {
				$route['methods'] = ['GET'];
			}

			return in_array($requestMethod, $route['methods']);
		});

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

			if (in_array($requestMethod, $allowedMethod)) {
				foreach ($matches as $key => $value) {
					$this->request->get[$key] = $value;
				}

				$this->request->get['route'] = $route['action'];

				$pathMatchFound = true;
			} else {
				header('HTTP/1.1 405');
			}
		}

		if ($pathMatchFound === false && isset($this->request->get['route'])) {
			unset($this->request->get['route']);
		}
	}
}
