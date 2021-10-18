<?php
class ControllerMiddlewaresLogs extends Controller {
	/** @var string Request identifier */
	private static $request_id;

	public function getRequestId() {
		return self::$request_id;
	}

	public function before() {
		$logs = array();

		if ($this->jwt) {
			$logs = array_merge($logs, array(
				'iss' => $this->jwt->iss,
				'sub' => $this->jwt->sub
			));

			self::$request_id = hash('sha256', $this->jwt->sub . hrtime(true) . uniqid());
		} else {
			self::$request_id = hash('sha256', hrtime(true) . uniqid());
		}

		$logs = array_merge($logs, array(
			'request_id' => self::$request_id,
			'user_agent' => $this->request->server['HTTP_USER_AGENT'] ?? '',
			'timestamp' => microtime(true),
			'route' => $this->request->get['route'],
			'URL' => $this->request->server['REQUEST_URI'],
			'query_strings' => $this->request->get,
			'request_headers' => $this->request->headers,
			'request_body' => $this->request->json,
			'ip_address' => $this->request->server['REMOTE_ADDR']
		));

		$this->log->write($logs);
	}

	public function after() {
		$logs = array(
			'request_id' => self::$request_id
		);

		if ($this->jwt) {
			$logs = array_merge($logs, array(
				'iss' => $this->jwt->iss,
				'sub' => $this->jwt->sub
			));
		}

		$logs = array_merge($logs, array(
			'user_agent' => $this->request->server['HTTP_USER_AGENT'],
			'ip_address' => $this->request->server['REMOTE_ADDR'],
			'timestamp' => microtime(true),
			'route' => $this->request->get['route'],
			'response_headers' => $this->response->getHeaders(),
			'response_body' => $this->response->getOutput()
		));

		$this->log->write($logs);
	}
}
