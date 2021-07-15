<?php

use Firebase\JWT\JWT;

class ModelCredentialsToken extends Model {

	/**
	 * Armazena o access_token e refresh_token em tabela para consulta
	 * de validação.
	 *
	 * @todo integrar ao banco de dados, cuja tabela deverá possuir os campos
	 * 	access_token e refresh_token do tipo TEXT, e status do tipo tinyint
	 *
	 * @return void
	 */
	public function addToken($access_token, $refresh_token) {
		$data = array(
			'access_token' => $access_token,
			'refresh_token' => $refresh_token,
			'status' => 1
		);

		$this->cache->set(sha1($access_token), $data);
		$this->cache->set(sha1($refresh_token), $data);
	}

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
		$user_id = 1;
		return $client_id === "opencartbrasil" && $client_secret === "v5market" ? $user_id : false;
	}

	/**
	 * Valida se o token existe no banco de dados
	 *
	 * @todo Integrar ao banco de dados
	 *
	 * @param string $refresh_token
	 *
	 * @return bool
	 */
	public function refreshTokenIsValid($refresh_token) {
		$data = $this->cache->get(sha1($refresh_token));

		try {
			if ($data && $data['status']) {
				JWT::decode(
					$data['refresh_token'],
					$this->config->get('secret_key'),
					array('HS256')
				);

				return true;
			}
		} catch (Exception $ignored) {
			return false;
		}

		return false;
	}

	/**
	 * Gera um novo token de acesso com base no dados do usuário
	 *
	 * @todo Integrar ao banco de dados
	 *
	 * @param int $user_id Identificador o usuário no banco de dados
	 * @param int $expire_at Tempo para expiração do token
	 *
	 * @return string
	 */
	public function generateToken($user_id, $expire_at = 3600) {
		$time = time();

		$client_id = 'opencartbrasil';
		$client_secret = 'v5market';
		$username = 'username';
		$application_name = 'Desktop-MS-1307';

		$jti_hash = sprintf('%s:%s:%s', $client_id, microtime(true), uniqid());
		$jti = hash_hmac('sha256', $jti_hash, $client_secret);

		$payload = array(
			'iss' => $this->config->get('config_url'),
			'iat' => $time,
			'sub' => $username,
			'exp' => $time + $expire_at,
			'jti' => $jti,
			'application_name' 	=> $application_name,
		);

		return JWT::encode($payload, $this->config->get('secret_key'));
	}

	/**
	 * Desabilita access_token e refresh_token após geração de um token
	 * de acesso
	 *
	 * @todo Realizar busca no banco de dados e alterar o valor do campo
	 * 	*status* para false. Ver o método "addToken"
	 *
	 * @param string $refresh_token
	 *
	 * @return void
	 */
	public function disableRefreshToken($refresh_token) {
		$data = array(
			'refresh_token' => $refresh_token,
			'status' => 0
		);

		$this->cache->set(sha1($refresh_token), $data);
	}
}
