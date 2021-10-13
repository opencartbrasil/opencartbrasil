<?php
class ControllerMiddlewaresResponseTime extends Controller {
	public function before() {
		$this->response->addHeader('X-Request-Time-Start: ' . microtime(true));
	}

	public function after() {
		$this->response->addHeader('X-Request-Time-End: ' . microtime(true));
	}
}
