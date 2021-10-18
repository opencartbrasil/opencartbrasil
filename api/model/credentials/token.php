<?php
use Firebase\JWT\JWT;

class ModelCredentialsToken extends Model {
	/**
	 * Add token
	 *
	 * @param int $api_key_id
	 * @param string $access_token
	 * @param string $refresh_token
	 * @param int $refresh_token Tempo de expiração do refresh token
	 *
	 * @return void
	 */
	public function addToken(int $api_key_id, $access_token, $refresh_token, int $refresh_expire = 0) {
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'api_token` WHERE `api_key_id` = "' . $api_key_id . '"');

		$this->db->query('
			INSERT INTO `' . DB_PREFIX . 'api_token`
			SET `api_key_id` = "' . $api_key_id . '",
				`access_token` = "' . $this->db->escape($access_token) . '",
				`refresh_token` = "' . $this->db->escape($refresh_token) . '",
				`refresh_expire` = "' . $refresh_expire . '",
				`status` = 1
		');
	}

	/**
	 * Login to API
	 *
	 * @param string $consumer_key
	 * @param string $consumer_secret
	 *
	 * @return bool|array
	 */
	public function login($consumer_key, $consumer_secret) {
		$user_info = $this->db->query('
			SELECT `api_key_id`
			FROM `' . DB_PREFIX . 'api_key`
			WHERE `consumer_key` = "' . $this->db->escape($consumer_key) . '"
			  AND `consumer_secret` = "' . $this->db->escape($consumer_secret) . '"
			  AND `status` = 1
		');

		return ($user_info->num_rows) ? intval($user_info->row['api_key_id']) : false;
	}

	/**
	 * Validates if token exists in database
	 *
	 * @param string $refresh_token
	 * @param int $expire_at
	 *
	 * @return bool
	 */
	public function refreshTokenIsValid($refresh_token, int $expire_at = 0) {
		$query = $this->db->query('
			SELECT *
			FROM `' . DB_PREFIX . 'api_token`
			WHERE `refresh_token` = "' . $this->db->escape($refresh_token) . '"
			  AND `refresh_expire` >= "' . (time() + $expire_at) . '"
			  AND `status` = 1
		');

		try {
			if ($query->num_rows) {
				JWT::decode(
					$query->row['refresh_token'],
					$this->config->get('api_secret_key'),
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
	 * Check if the access token is active
	 *
	 * @param string $access_token
	 *
	 * @return bool
	 */
	public function accessTokenIsValid($access_token) {
		if (empty($access_token)) {
			return false;
		}

		$query = $this->db->query('
			SELECT
				at.api_key_id,
				at.access_token,
				at.refresh_token,
				at.refresh_expire,
				ak.status
			FROM `' . DB_PREFIX . 'api_token` at
			LEFT JOIN `' . DB_PREFIX . 'api_key` ak ON (ak.api_key_id = at.api_key_id)
			WHERE `access_token` = "' . $this->db->escape($access_token) . '"
				AND ak.`status` = 1
		');

		return !!$query->num_rows;
	}

	/**
	 * Generate a new access token based on user data
	 *
	 * @param int $user_id
	 * @param int $expire_at
	 *
	 * @return string
	 */
	public function generateToken(int $api_key_id, int $expire_at = 3600) {
		$time = time();

		$user_info = $this->db->query('
			SELECT ak.api_key_id, ak.consumer_key, ak.consumer_secret, ak.permissions
			FROM `' . DB_PREFIX . 'api_key` ak
			WHERE ak.status = 1
			  AND ak.api_key_id = "' . $api_key_id . '"
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
			'sub' => $user_info->row['api_key_id'],
			'exp' => $time + $expire_at,
			'jti' => $jti,
			'scope' => $user_info->row['permissions']
		);

		return array(
			'jwt' => JWT::encode($payload, $this->config->get('api_secret_key')),
			'exp' => $time + $expire_at,
		);
	}

	/**
	 * Writes a user's token generation history
	 *
	 * @param int $api_key_id
	 * @param string $type
	 *
	 * @return void
	 */
	public function addHistory(int $api_key_id, string $type) {
		$this->db->query('
			INSERT INTO `' . DB_PREFIX .'api_history`
			SET `api_key_id` = "' . $api_key_id . '",
				`type` = "' . $this->db->escape($type) . '",
				`date_added` = NOW(),
				`ip_address` = "' . $this->db->escape($this->request->server['REMOTE_ADDR']) . '"
		');
	}
}
