<?php
class ControllerStatusCodeServiceUnavailable extends Controller {
	public function index() {
		header('HTTP/1.1 503 Service Unavailable');
	}
}
