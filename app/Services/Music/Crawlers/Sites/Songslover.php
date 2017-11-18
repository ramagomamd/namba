<?php

namespace App\Services\Music\Crawlers\Sites;

use Illuminate\Support\Facades\DB;
use Exception;

class Songslover 
{
	public static function scan($client, $data)
	{
		if ($data['type'] == 'albums') {
			// dd($data['type']);
			return self::albums($client, $data);
		} elseif ($data['type'] == 'singles') {
			return self::singles($client, $data);
		}
		return null;
	}

	public static function albums($client, $data)
	{
		// Start crawling the search results:
		$page = 1;
		$results = null;
		$done = false;

		$last = DB::table('albums_crawler')->whereSiteName($data['site'])->latest('id')->first();
// 		dd($last);

		$crawler = $client->request('GET', $data['url']);

		while ((is_null($results) || $page <= $data['pages']) && $done == false) {
			// If we are moving to another page then click the paging link:
			if ($page > 1) {
				// $link = $crawler->selectLink($page)->link();
				// dd($link);
				// $crawler = $client->click($link);
				$crawler = $client->request('GET', "{$data['url']}/page/{$page}");
			}
			// dd($crawler);
			$results[] = $crawler->filter('article.item-list')->each(function ($result) use ($client, $data)  {
				try {
					$link = $result->filter('.post-thumbnail a')->first()->attr('href');
					$crawler = $client->request('GET', $link);
					$mp3s = $crawler->filter('a[href$=".mp3"]')->each(function ($mp3, $index) {
						if ($index % 2 === 0) {
							return $mp3->attr('href');
						}
					});

					$mp3s = collect($mp3s)->reject(null);

					$zip = $crawler->filter('a[href$=".zip"]')->first()->attr('href');
				} catch (Exception $e) {
					$link = null;
					$mp3s = null;
				}
				// dd($mp3s);

				if (!empty($mp3s) || !is_null($mp3s)) {
					try{
					    $title = $crawler->filter('h1.name span')->first()->text();
					} catch(Exception $e) { 
					    // Node list is empty
					    $title = null;
					}
					try{
					    $cover = $crawler->filter('.entry p img')->first()->attr('data-lazy-src');
					    $cover = str_replace('//', 'https://', $cover);
					} catch(Exception $e) { 
					    // Node list is empty
					    $cover = null;
					}
					
					if ($mp3s->count() && !is_null($title)) {
						try {
							$album = DB::table('albums_crawler')
									->insertGetId([
										'title' => $title,
										'link' => $link,
										'zip' => $zip,
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
						} catch (Exception $e) {
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

	public static function singles($client, $data)
	{
		// Start crawling the search results:
		$page = 1;
		$results = null;
		$done = false;

		$last = DB::table('singles_crawler')->whereSiteName($data['site'])->latest('id')->first();
// 		dd($last);

		$crawler = $client->request('GET', $data['url']);

		while ((is_null($results) || $page <= $data['pages']) && $done == false) {
			// If we are moving to another page then click the paging link:
			if ($page > 1) {
				// $link = $crawler->selectLink($page)->link();
				// dd($link);
				// $crawler = $client->click($link);
				$crawler = $client->request('GET', "{$data['url']}/page/{$page}");
			}
			// dd($crawler);
			$results[] = $crawler->filter('article.item-list')->each(function ($result) use ($client, $data)  {
				try {
					$link = $result->filter('.post-thumbnail a')->first()->attr('href');
					$crawler = $client->request('GET', $link);
					$mp3s = $crawler->filter('a[href$=".mp3"]')->each(function ($mp3, $index) {
						if ($index % 2 === 0) {
							return $mp3->attr('href');
						}
					});

					$mp3s = collect($mp3s)->reject(null);
				} catch (Exception $e) {
					$link = null;
					$mp3s = null;
				}
				// dd($mp3s);

				if (!empty($mp3s) || !is_null($mp3s)) {
					try{
					    $title = $crawler->filter('h1.name span')->first()->text();
					} catch(Exception $e) { 
					    // Node list is empty
					    $title = null;
					}
					try{
					    $cover = $crawler->filter('.entry p img')->first()->attr('data-lazy-src');
					    $cover = str_replace('//', 'https://', $cover);
					} catch(Exception $e) { 
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