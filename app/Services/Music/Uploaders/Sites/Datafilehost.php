<?php

namespace App\Services\Music\Uploaders\Sites;

use App\Models\Music\Track\Track;
use App\Models\Music\Album\Album;
use Exception;

class Datafilehost 
{
	public static function scan($client, $data)
	{
		if ($data['type'] === 'tracks') {
			return self::tracks($client, $data);
		} elseif ($data['type'] === 'albums') {
			return self::albums($client, $data);
		}
	}

	public static function tracks($client, $data)
	{
		$tracks = Track::whereNotNull('trackable_id')
                        ->whereNotNull('slug')
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->reject(function($track) {
                        	return $track->links->isNotEmpty();
                        });

        $uploads = $tracks->each(function($track) use ($client, $data) {
            if ($track->hasMedia('file') && !is_null($track->trackable)) {
				try {
						$crawler = $client->request('GET', $data['link']);
						// select the form and fill in some values
						$form = $crawler->selectButton('Upload!')->form();
						$form['upfile']->upload($track->file->getPath());
						$crawler = $client->submit($form);
						$url = $crawler->filter('div.col-sm-8 td a')->first()->attr('href');

						$upload = $track->links()->create([
							'site_name' => $data['site'],
							'url' => $url,

						]);

					return $upload->url;
				} catch (Exception $e) {
            		return null;
            	}
            }
        })->reject(null);

        return $uploads->count();
	}

	public static function albums($client, $data)
	{
		$albums = Album::with('tracks')
                    ->has('tracks')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->reject(function($album) {
                    	return $album->links->isNotEmpty();
                    });

        $albums->each(function($album) use ($client, $data) {
        	if (!is_null($album->zip)) {
					
				if ($zip = $album->zip) {
					try {
						$crawler = $client->request('GET', $data['link']);
						// select the form and fill in some values
						$form = $crawler->selectButton('Upload!')->form();
						$form['upfile']->upload($album->zip->getPath());
						$crawler = $client->submit($form);
						$url = $crawler->filter('div.col-sm-8 td a')->first()->attr('href');

						$upload = $album->links()->create([
							'site_name' => $data['site'],
							'url' => $url,

						]);
						$zip->delete();
						return $upload->url;
					} catch (Exception $e) {
						$zip->delete();
	            		return null;
	            	}
				}
				return false;
			}
			return false;
        });
	}
}