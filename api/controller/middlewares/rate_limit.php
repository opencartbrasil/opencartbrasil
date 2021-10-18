<?php
class ControllerMiddlewaresRateLimit extends Controller {
	public function before() {
		if (in_array($this->request->get['route'], $this->config->get('ignored_routers'))) {
			return;
		}

		if (!isset($this->jwt) && !isset($this->jwt->jti)) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'invalid_access_token',
						'message' => 'Invalid access_token.'
					)
				)
			)));

			return new Action('status_code/bad_request');
		}

		$request_per_minute = $this->config->get('api_request_per_minute');

		$cache = new Cache($this->config->get('cache_engine'), $this->config->get('api_cache_expire'));

		$token_hash = $this->jwt->jti;

		$details = $cache->get($token_hash);

		if ($details === false) {
			$details = $this->generate_data();
		}

		$expired = time() > $details['expire_at'];

		if ($expired === true) {
			$details = $this->generate_data();
			$rate_remaining = $request_per_minute - $details['count'];
		}

		$rate_remaining = $request_per_minute - $details['count'];
		$rate_reset = $details['expire_at'] - time();

		if ($rate_remaining < 0 && $expired === false) {
			header('X-RateLimit-Limit: ' . $request_per_minute);
			header('X-RateLimit-Reset: ' . $rate_reset);
			header('X-RateLimit-Remaining: 0');

			$this->response->setOutput(json_encode(array(
				'success' => false,
				'errors' => array(
					array(
						'code' => 'rate_limit',
						'message' => 'You made many requests in a short time.'
					)
				)
			)));

			return new Action('status_code/too_many_requests');
		}

		$details['count']++;

		header('X-RateLimit-Limit: ' . $request_per_minute);
		header('X-RateLimit-Reset: ' . $rate_reset);
		header('X-RateLimit-Remaining: ' . $rate_remaining);

		$cache->set($token_hash, $details);
	}

	/**
	 * Generates new data with the count and expiration date
	 *
	 * @return void
	 */
	private function generate_data() {
		return array(
			'count' => 1,
			'expire_at' => time() + $this->config->get('api_cache_expire')
		);
	}
}
