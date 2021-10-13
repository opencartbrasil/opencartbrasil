<?php
class ControllerStatusCodeTooManyRequests extends Controller {
	public function index() {
		header('HTTP/1.1 429 Too Many Requests');
	}
}
