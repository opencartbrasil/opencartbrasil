<?php
class ControllerStatusCodeBadRequest extends Controller {
	public function index() {
		header('HTTP/1.1 400 Bad Request');
	}
}
