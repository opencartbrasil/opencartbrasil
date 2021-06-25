<?php

class ModelCredentialsToken extends Model {

	/**
	 * Realiza login no sistema de API
	 *
	 * @param string $client_id
	 * @param string $client_secret
	 *
	 * @todo Integrar ao banco de dados
	 *
	 * @return bool|array
	 */
	public function login($client_id, $client_secret) {
		return true;
	}

	/**
	 * Valida se o token existe no banco de dados
	 *
	 * @param string $token
	 *
	 * @return bool
	 */
	public function isValid($token) {
		return true;
	}
}
