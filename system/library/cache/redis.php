<?php
namespace Cache;
class Redis {
	private $expire;
	private $redis;

	public function __construct($expire) {
		$this->expire = $expire;

		$this->redis = new \Redis();
		$this->redis->pconnect(CACHE_HOSTNAME, CACHE_PORT);
	}

	public function get($key) {
		$data = $this->redis->get(CACHE_PREFIX . $key);

		return json_decode($data, true);
	}

	public function set($key, $value) {
		$status = $this->redis->set(CACHE_PREFIX . $key, json_encode($value));

		if ($status) {
			$this->redis->expire(CACHE_PREFIX . $key, $this->expire);
		}

		return $status;
	}

	public function delete($key) {
		$this->redis->del(CACHE_PREFIX . $key);
	}
}