<?php

class ModelMiddlewaresIp extends Model {

	/**
	 * Verifica, no banco de dados, de um determinado IP está bloqueado
	 *
	 * @param string $ip
	 *
	 * @return bool
	 */
	public function hasBlocked($ip) {
		return false;
	}
}
