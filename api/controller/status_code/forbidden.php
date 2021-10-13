<?php
class ControllerStatusCodeForbidden extends Controller {
	public function index() {
		header('HTTP/1.1 403 Forbidden');
	}
}
