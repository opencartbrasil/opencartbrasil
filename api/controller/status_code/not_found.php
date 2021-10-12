<?php
class ControllerStatusCodeNotFound extends Controller {
	public function index() {
		header('HTTP/1.1 404 Not Found');
	}
}
