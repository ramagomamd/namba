<?php

namespace App\Services\Music\Crawlers\Sites;

use Illuminate\Support\Facades\DB;
use Exception;

class Slikour 
{
	public static function scan($client, $data)
	{
		if ($data['type'] == 'albums') {
			return self::albums($client, $data);
		} elseif ($data['type'] == 'singles') {
			return self::singles($client, $data);
		}
	}

	public static function albums($client, $data)
	{
		$crawler = $client->request('GET', $data['url']);

		// Use a CSS filter to select only the result links:
		$results[] = $crawler->filter('#slikourDownloadsAlbums .panel.panel-media.shadow-panel')
			->each(function ($result) use ($client, $data) {
			$link = "https://www.slikouronlife.co.za" . $result->filter('h3.media-description a')->first()->attr('href');

			$crawler = $client->request('GET', $link);

			$zip = $crawler->filter('div.col-md-8 a[href*="download-album"]')->each(function ($mp3, $index) {
				return "https://www.slikouronlife.co.za" . $mp3->attr('href');
			});

			try{
			    $artists = trim($crawler->filter('p.profile-info a.slikour-ajax-nav-btn')->first()->text());
			    $title = trim($crawler->filter('h2.article-heading')->first()->text());
			    $full_title = "{$artists} - {$title}";
			} catch(Exception $e) {
			    $full_title = null;
			}

			try{
			    $cover = $crawler->filter('div.article-image a img')->first()->attr('src');
			} catch(Exception $e) { 
			    $cover = null;
			}

			if (!is_null($zip) && !is_null($full_title)) {
				try {
					$album = DB::table('albums_crawler')
							->insertGetId([
								'title' => $full_title,
								'link' => $link,
								'zip' => $zip[0],
								'site_name' => $data['site'],
								'cover' => $cover,
								'category' => $data['category'],
								'genre' => $data['genre']
							]);
					return $link;
				}  catch (Exception $e) {
					return $link;
				}
			}

			return $link;
		});

		return count(array_collapse($results));
	}

	public static function singles($client, $data)
	{
		$crawler = $client->request('GET', $data['url']);
		// Use a CSS filter to select only the result links:
		$results[] = $crawler->filter('#slikourDownloadsSongs .panel.panel-media.shadow-panel')
			->each(function ($result) use ($client, $data) {
			$link = "https://www.slikouronlife.co.za" . $result->filter('h3.media-description a')->first()->attr('href');
			$crawler = $client->request('GET', $link);
			$mp3s = $crawler->filter('div.col-md-8 a[href*="download-song"]')->each(function ($mp3, $index) {
				return "https://www.slikouronlife.co.za" . $mp3->attr('href');
			});

			$mp3s = collect($mp3s)->reject(null);
			// dd($mp3s);
			try{
			    $artists = trim($crawler->filter('p.profile-info a.slikour-ajax-nav-btn')->first()->text());
			    $title = trim($crawler->filter('h2.article-heading')->first()->text());
			    $full_title = "{$artists} - {$title}";
			} catch(Exception $e) { // I guess its InvalidArgumentException in this case
			    // Node list is empty
			    $full_title = null;
			}

			try{
			    $cover = $crawler->filter('div.article-image a img')->first()->attr('src');
			} catch(Exception $e) { 
			    $cover = null;
			}

			if ($mp3s->isNotEmpty()) {
				if ($mp3s->count() == 1 && !is_null($full_title)) {
					try {
						$single = DB::table('singles_crawler')
								->insertGetId([
									'title' => $full_title,
									'link' => $link,
									'site_name' => 'slikour',
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
						// dd("Skipped single");
						return $link;
					}
				} 
			} 

			return $link;
		});

		return count(array_collapse($results));
	}
}