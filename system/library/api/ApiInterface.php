<?php
namespace Api;

/**
 * Api Interface
 */
interface ApiInterface
{
	/**
	 * Send only Request
	 *
	 * @param string $method  	HTTP method
	 * @param string $endpoint 	Endpoint consumer
	 * @param array	 $params	Request headers and Request body
	 * @param bool 	 $assoc		Associative array from Response
	 *
	 * @return mixed
	 */
	public function send(string $method, string $endpoint, array $params = [], bool $assoc = false);

	/**
	 * Attach Requests
	 *
	 * @param string $method  	HTTP method
	 * @param string $endpoint 	Endpoint consumer
	 * @param array	 $params	Request headers and Request body
	 */
	public function attach(
		string $method,
		string $endpoint = '',
		array $params = [],
		string $uri = ''
	);

	/**
	 * Send one or more Requests asynchronous
	 *
	 * @param bool $assoc return or not associative array from Response
	 *
	 * @return mixed
	 */
	public function async(bool $assoc = false);

	/**
	 *
	 * @param int  $concurrency Maximum number of simultaneous requests 
	 * @param bool $assoc		Associative array from Response
	 *
	 * @return mixed
	 */
	public function multi(int $concurrency = 10, bool $assoc = false);

	/**
	 * Convert data to query string parameters url
	 *
	 * @param array $data Values to conversion
	 *
	 * @return string
	 */
	public function toQuery(array $data);	
}