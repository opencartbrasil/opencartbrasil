<?php
namespace Api;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

require_once __DIR__ . '/guzzle/vendor/autoload.php';

/**
 * Api class
 */
class Api implements ApiInterface
{
	/** @var string */
	private $url;

	/** @var array */
	private $options = [];

	/** @var object|Client */
	private $client;

	/** @var array */
	private $requests = [];

	public function __construct(string $url = '', array $options = [])
	{
		$this->url = $url;
		
		$this->options = $options;

		$this->client = new Client(['base_uri' => $url], $options);
	}

	public function send(string $method, string $endpoint, array $params = [], bool $assoc = false)
	{
		$uri = $this->makePath($endpoint);

		$request = $this->client->request($method, $uri, array_merge(['http_errors' => false], $params));

		$response['code'] = $request->getStatusCode();

		$response['body'] = json_decode($request->getBody(), $assoc);

		return $response;
	}

	public function attach(
		string $method,
		string $endpoint = '',
		array $params = [],
		string $uri = ''
	) {
		$query = isset($params['query']) ? $params['query'] : [];
		
		$url = $this->makePath($endpoint, $query, $uri);
		
		$headers = isset($params['headers']) ? $params['headers'] : [];
		
		$body = isset($params['body']) ? $params['body'] : null;
		
		$this->requests[] = new Request($method, $url, $headers, $body);
	}

	public function async(bool $assoc = false) 
	{
		$results = [];

		if (!empty($this->requests)) {
			foreach ($this->requests as $key => $request) {
				$promises[$key] = $this->client->sendAsync($request, ['http_errors' => false]);
			}
	
			$responses = Promise\unwrap($promises);
	
			foreach ($responses as $key => $response) {
				$results[$key]['body'] = json_decode($response->getBody()->getContents(), $assoc);
	
				$results[$key]['code'] = $response->getStatusCode();
			}
		}

		return $results;
	}

	public function multi(int $concurrency = 10, bool $assoc = false)
	{
		$results = [];

		$pool = new Pool($this->client, $this->requests, [
			'concurrency' => $concurrency,
			'fulfilled' => function (Response $response, $id) use (&$results, $assoc) {
				$results[$id]['body'] = json_decode($response->getBody()->getContents(), $assoc);

				$results[$id]['code'] = $response->getStatusCode();
			},
			'rejected' => function ($reason, $id) use (&$results, $assoc) {
				$results[$id]['body'] = json_decode($reason->getResponse()->getBody()->getContents(), $assoc);

				$results[$id]['code'] = $reason->getResponse()->getStatusCode();
			},
		]);

		$pool->promise()->wait();

		return $results;
	}

	/**
	 * Format full url to consumer Api
	 *
	 * @param string $url 		Optional value
	 * @param string $endpoint 	Optional value
	 * @param string $params 	Optional value
	 *
	 * @return string
	 */
	private function makePath(string $endpoint = '', array $params = [], string $url = '')
	{
		if (!preg_match("/^\//", $endpoint)) {
            $endpoint = '/' . $endpoint;
		}
		
		$url = empty($url) ? $this->url : $url;

		$uri = $url . $endpoint . $this->toQuery($params);

        return $uri;
	}

	public function toQuery(array $data)
	{		
		return empty($data) ? '' : '?' . http_build_query($data, '', '&');
	}
}

?>