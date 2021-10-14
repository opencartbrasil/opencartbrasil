<?php
class ControllerStatusCodeUnauthorized extends Controller {
	public function index() {
		header('HTTP/1.1 401 Unauthorized');
	}
}
