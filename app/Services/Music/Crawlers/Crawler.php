<?php

namespace App\Services\Music\Crawlers;

use Goutte\Client;
use GuzzleHttp\Client as Guzzle;
use App\Services\Music\Crawlers\Sites\Fakaza;
use App\Services\Music\Crawlers\Sites\Slikour;
use App\Services\Music\Crawlers\Sites\Songslover;
use App\Services\Music\Crawlers\Sites\Lulamusic;
use App\Services\Music\Crawlers\Sites\Tooxclusive;

class Crawler
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
			case "lulamusic":
				$results = Lulamusic::scan($client, $data);
				break;
			case "slikour":
				$results = Slikour::scan($client, $data);
				break;
			case "fakaza":
				$results = Fakaza::scan($client, $data);
				break;
			case "tooxclusive":
				$results = Tooxclusive::scan($client, $data);
				break;
			case "songslover":
				$results = Songslover::scan($client, $data);
				break;
		}

		return $results;
	}
}