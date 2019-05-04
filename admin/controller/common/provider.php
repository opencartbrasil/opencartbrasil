<?php
class ControllerCommonProvider extends Controller {
	private $route;
	private $params = array();
	private $attached = array();
	private $detached = array();	

	public function index()
	{
		$this->route = $this->hasRequest('route') ? $this->request->get['route'] : '';

		return $this;
	}
	
	public function parser(array $params)
	{
		foreach ($params as $key => $param) {
			$this->params[$key] = $this->hasRequest($key) ? $this->request->get[$key] : $param;
		}
	}

	public function setParser(string $key, string $value)
	{
		$this->params[$key] = $value;
	}

	public function getParser($key)
	{
		return isset($this->params[$key]) ? $this->params[$key] : '';
	}

	public function hasParser($key)
	{
		return isset($this->params[$key]);
	}

	public function hasRequest($key)
	{
		return isset($this->request->get[$key]);
	}

	public function getParams()
	{
		return $this->params;
	}

	public function attach(string $key, string $value)
	{
		$this->attached[$key] = $value;

		return $this;
	}

	public function detach(string $key)
	{
		$this->detached[$key] = '';

		return $this;
	}

	public function link(string $route = '', array $args = array())
	{
		$route = empty($route) ? $this->route : $route;

		$params = array_merge($this->request->get, $this->detached, $this->attached, $args);
		
		if ($this->hasRequest('route')) {
			unset($params['route']);
		}

		$query = array_filter($params, function($value) {
			return $value !== '';
		});
		
		$url = http_build_query($query, '', '&');

		return $this->url->link($route, $url);
	}
}