<?php

class ModelWebHookAdvancedWebHook extends Model {
	public function getHooks($type) {
		return [
			[
				'url' => 'https://eniq4vack1w5p.x.pipedream.net/',
				'headers' => [
					'X-Count: One'
				],
			],
			[
				'url' => 'https://enpms603gtdc.x.pipedream.net/',
				'headers' => [
					'X-Count: Two'
				],
			],
			[
				'url' => 'https://httpstat.us/401',
				'headers' => [],
			],
			[
				'url' => 'https://httpstat.us/404',
				'headers' => [],
			],
			[
				'url' => 'https://httpstat.us/500',
				'headers' => [],
			]
		];
	}

	public function saveRequest($type, $request, $response, $status_code) {

	}
}
