<?php

namespace App\Services\Music\Uploaders;

use Goutte\Client;
use GuzzleHttp\Client as Guzzle;
use App\Services\Music\Uploaders\Sites\Datafilehost;

class Upload
{

	public function process(array $data)
	{
		$client = new Client();
		$guzzle = new Guzzle([
			'timeout' => 60,
			'verify' => false,
		]);
		$client->setClient($guzzle);

		switch($data['site']) {
			case "datafilehost":
				$results = Datafilehost::scan($client, $data);
				break;
		}

		return $results;
	}
}