<?php

use Firebase\JWT\JWT;

class ModelCredentialsToken extends Model {

	/**
	 * Armazena o access_token e refresh_token em tabela para consulta
	 * de validação.
	 *
	 * @param int $user_id User ID da tabela "oc_api"
	 * @param string $access_token
	 * @param string $refresh_token
	 * @param int $refresh_token Tempo de expiração do refresh token
	 *
	 * @return void
	 */
	public function addToken(int $user_id, $access_token, $refresh_token, int $refresh_expire = 0) {
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'api_tokens` WHERE `user_id` = "' . $user_id . '"');

		$this->db->query('
			INSERT INTO `' . DB_PREFIX . 'api_tokens`
			SET `user_id` = "' . $user_id . '",
				`access_token` = "' . $this->db->escape($access_token) . '",
				`refresh_token` = "' . $this->db->escape($refresh_token) . '",
				`refresh_expire` = "' . $refresh_expire . '",
				`status` = 1
		');
	}

	/**
	 * Realiza login no sistema de API
	 *
	 * @param string $consumer_key
	 * @param string $consumer_secret
	 *
	 * @return bool|array
	 */
	public function login($consumer_key, $consumer_secret) {
		$user_info = $this->db->query('
			SELECT `user_id`
			FROM `' . DB_PREFIX . 'api_keys`
			WHERE `consumer_key` = "' . $this->db->escape($consumer_key) . '"
			  AND `consumer_secret` = "' . $this->db->escape($consumer_secret) . '"
			  AND `status` = 1
		');

		return ($user_info->num_rows) ? intval($user_info->row['user_id']) : false;
	}

	/**
	 * Valida se o token existe no banco de dados
	 *
	 * @param string $refresh_token
	 * @param int $expire_at
	 *
	 * @return bool
	 */
	public function refreshTokenIsValid($refresh_token, int $expire_at = 0) {
		$query = $this->db->query('
			SELECT *
			FROM `' . DB_PREFIX . 'api_tokens`
			WHERE `refresh_token` = "' . $this->db->escape($refresh_token) . '"
			  AND `refresh_expire` >= "' . (time() + $expire_at) . '"
			  AND `status` = 1
		');

		try {
			if ($query->num_rows) {
				JWT::decode(
					$query->row['refresh_token'],
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
	 * Verifica se o token de acesso está ativo
	 *
	 * @param string $access_token
	 *
	 * @return bool
	 */
	public function accessTokenIsValid($access_token) {
		$query = $this->db->query('
			SELECT * FROM `' . DB_PREFIX . 'api_tokens`
			WHERE `access_token` = "' . $this->db->escape($access_token) . '"
			  AND `status` = 1');

		return !!$query->num_rows;
	}

	/**
	 * Gera um novo token de acesso com base no dados do usuário
	 *
	 * @param int $user_id Identificador o usuário no banco de dados
	 * @param int $expire_at Tempo para expiração do token
	 *
	 * @return string
	 */
	public function generateToken(int $user_id, int $expire_at = 3600) {
		$time = time();

		$user_info = $this->db->query('
			SELECT ak.user_id, ak.consumer_key, ak.consumer_secret, ak.permissions, a.username
			FROM `' . DB_PREFIX . 'api_keys` ak
			LEFT JOIN `' . DB_PREFIX . 'api` a ON (a.api_id = ak.user_id)
			WHERE a.status = 1
			  AND ak.status = 1
			  AND ak.user_id = "' . $user_id . '"
			LIMIT 1
		');

		if (!$user_info->num_rows) {
			throw new \UnexpectedValueException('User not found');
		}

		$jti_hash = sprintf('%s:%s:%s', $user_info->row['consumer_key'], microtime(true), uniqid());
		$jti = hash_hmac('sha256', $jti_hash, $user_info->row['consumer_secret']);

		$payload = array(
			'iss' => $this->config->get('config_url'),
			'iat' => $time,
			'sub' => $user_info->row['user_id'],
			'exp' => $time + $expire_at,
			'jti' => $jti,
			'client_id' => $user_info->row['username'],
			'scope' => $user_info->row['permissions']
		);

		return [
			'jwt' => JWT::encode($payload, $this->config->get('secret_key')),
			'exp' => $time + $expire_at,
		];
	}
}
