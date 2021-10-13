<?php
class ControllerStatusCodeMethodNotAllowed extends Controller {
	public function index() {
		header('HTTP/1.1 405 Method Not Allowed');
	}
}
