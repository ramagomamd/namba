<?php

namespace App\Services\Music\Crawlers\Sites;

use Illuminate\Support\Facades\DB;
use Exception;

class Lulamusic 
{
	public static function scan($client, $data)
	{
		// Start crawling the search results:
		$page = 1;
		$results = null;
		$done = false;

		$last = DB::table('singles_crawler')->whereSiteName($data['site'])->oldest('id')->first();

		$crawler = $client->request('GET', $data['url']);

		while ((is_null($results) || $page <= $data['pages']) && $done == false) {
			// If we are moving to another page then click the paging link:
			if ($page > 1) {
				// $link = $crawler->selectLink($page)->link();
				// dd($link);
				// $crawler = $client->click($link);
				$crawler = $client->request('GET', "{$data['url']}?page={$page}");
			}
			// Use a CSS filter to select only the result links:
			$results[] = $crawler->filter('div.media-body h4.h5 a')->each(function ($result) use ($client, $data) {
				$link = $result->attr('href');
				$crawler = $client->request('GET', $link);
				$mp3s = $crawler->filter('input[name=url]')->each(function ($mp3, $index) {
					return $mp3->attr('value');
				});

				$mp3s = collect($mp3s)->reject(null);

				if ($mp3s->isNotEmpty()) {
					try{
					    $title = $crawler->filter('div.panel-heading h1.h3 strong')->first()->text();
					    $title = trim(str_ireplace(['stream and download', 'mp3'], '', $title));
					} catch(Exception $e) { // I guess its InvalidArgumentException in this case
					    // Node list is empty
					    $title = null;
					}
					try{
					    $cover = $crawler->filter('div.panel-body .col-md-12 img')->first()->attr('src');
					} catch(Exception $e) { // I guess its InvalidArgumentException in this case
					    // Node list is empty
					    $cover = null;
					}
					
					if ($mp3s->count() == 1 && !is_null($title)) {
						try {
							$single = DB::table('singles_crawler')
									->insertGetId([
										'title' => $title,
										'link' => $link,
										'site_name' => $data['site'],
										'cover' => $cover,
										'category' => $data['category'],
										'genre' => $data['genre']
									]);
							// dd($single);
							// Store  the mp3 to singles database
							$track = DB::table('tracks_crawler')->insertGetId([
								'link' => $mp3s->first(),
								'crawlable_id' =>  $single,
								'crawlable_type' => 'singles',
							]);
							return $link;
						} catch (Exception $e) {
							return $link;
						}
					} elseif ($mp3s->count() > 1 && !is_null($title)) {
						try {
							$album = DB::table('albums_crawler')
									->insertGetId([
										'title' => $title,
										'link' => $link,
										'site_name' => $data['site'],
										'cover' => $cover,
										'category' => $data['category'],
										'genre' => $data['genre']
									]);
							// dd($single);
							// Store  the mp3 to singles database
							$mp3s->each(function($mp3) use ($album) {
								$track = DB::table('tracks_crawler')->insertGetId([
									'link' => $mp3,
									'crawlable_id' =>  $album,
									'crawlable_type' => 'albums',
								]);
							});
							return $link;
						}  catch (Exception $e) {
							return $link;
						}
					}
				} else {
					return $link;
				}

				return $link;
			});

			if (!is_null($last)) {
				$last_link = $last->link;
				// dd($last_link);
				// dd($results);
				// dd(in_array($last_link, array_collapse($results)));
				if (in_array($last_link, array_collapse($results))) {
					$done = true;
					// dd($done);
				} 
			}

			$page++;
		}
		return count(array_collapse($results));
	}
}