<?php
class ModelMiddlewaresIp extends Model {

	/**
	 * Checks in database if IP is blocked
	 *
	 * @param string $ip
	 *
	 * @return bool
	 */
	public function hasBlocked($ip) {
		return false;
	}
}
