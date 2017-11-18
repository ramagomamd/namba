<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Music\Track\Track;
use App\Models\Music\Album\Album;
use App\Models\Music\Category\Category;
use App;
use URL;

class SitemapsController extends Controller
{
	public function tracks()
	{
		 // create sitemap
	    $sitemap_tracks = App::make("sitemap");

	    // set cache
	    // $sitemap_tracks->setCache('laravel.sitemap-posts', 3600);

	    // add items
	    // $tracks = DB::table('tracks')->orderBy('created_at', 'desc')->get();
	    $tracks = Track::whereNotNull('trackable_id')
		    			->whereNotNull('slug')
		    			->orderBy('created_at', 'desc')
		    			->get();

	    foreach ($tracks as $track)
	    {
	    	if (!is_null($track->trackable)) {
	    		if ($track->hasMedia('cover')) {
		    		$images = [
						[
							'url' => $track->getFirstMedia('cover')->getFullUrl(), 
							'title' => $track->full_title, 
							'caption' => $track->comment, 
							'geo_location' => 'Pretoria, South Africa'
						],
					];
		    	} else {
		    		$images = null;
		    	}

		        $sitemap_tracks->add($track->frontend_show_route, null, '0.7', 'monthly', $images);
	    	}
	    }

	    // show sitemap
	    return $sitemap_tracks->render('xml');
	}

	public function albums()
	{
		// create sitemap
	    $sitemap_albums = App::make("sitemap");

	    // set cache
	    // $sitemap_albums->setCache('laravel.sitemap-posts', 3600);
	    $sitemap_albums->add(URL::to('/singles'), null, '1.0', 'daily');
	    $sitemap_albums->add(URL::to('/albums'), null, '1.0', 'daily');

	    // add items
	    $albums = Album::has('tracks')->orderBy('created_at', 'desc')->get();

	    foreach ($albums as $album)
	    {
	    	if ($album->hasMedia('cover')) {
	    		$images = [
					[
						'url' => $album->getFirstMedia('cover')->getFullUrl(), 
						'title' => $album->full_title, 
						'caption' => $album->description, 
						'geo_location' => 'Pretoria, South Africa'
					],
				];
	    	} else {
	    		$images = null;
	    	}
	    	
	    	// dd($album);
	        $sitemap_albums->add($album->frontend_show_route, null, '0.7', 'monthly', $images);
	    }

	    // show sitemap
	    return $sitemap_albums->render('xml');
	}

	public function show()
	{
		// create sitemap
	    $sitemap = App::make("sitemap");

	    // set cache
	    // $sitemap->setCache('laravel.sitemap-index', 3600);
	    $sitemap->addSitemap(URL::route('albums-sitemap'));
	    $sitemap->addSitemap(URL::route('tracks-sitemap'));

	    // show sitemap
	    return $sitemap->render('sitemapindex');
	}
}
