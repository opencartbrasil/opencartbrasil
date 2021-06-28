<?php

class ControllerMiddlewaresRateLimit extends Controller {
	public function before() {
		$ignoredRoutes = [
			'credentials/token',
		];

		if (in_array($this->request->get['route'], $ignoredRoutes)) {
			return;
		}

		if (!isset($this->jwt) && !isset($this->jwt->jti)) {
			return new Action('status_code/bad_request');
		}

		$token_hash = $this->jwt->jti;

		$details = $this->cache->get($token_hash);

		if ($details === false) {
			$details = $this->generate_data();
		}

		$expired = time() > $details['expire_at'];

		if ($details['count'] >= $this->config->get('max_request_per_time') && $expired === false) {
			return new Action('status_code/too_many_requests');
		}

		if ($expired === true) {
			$details = $this->generate_data();
		} else {
			$details['count']++;
		}

		$this->cache->set($token_hash, $details);
	}

	/**
	 * Gera um novo dado com a contagem e a data de expiração
	 *
	 * @return void
	 */
	private function generate_data() {
		$expire_interval = new DateInterval($this->config->get('expiration_interval_format'));

		$expire_date = new DateTime();
		$expire_date->add($expire_interval);

		return [
			'count' => 1,
			'expire_at' => $expire_date->format('U')
		];
	}
}
