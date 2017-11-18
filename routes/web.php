<?php

use Goutte\Client;
use App\Models\Music\Track\Track;

Route::get('goutte', function() {

	$tracks = Track::take(2)->get();

	$tracks->each(function($track) {
		if ($track->hasMedia('file')) {
			// make a real request to an external site
			$client = new Client();
			$crawler = $client->request('GET', 'https://www.datafilehost.com/');
			// select the form and fill in some values
			$form = $crawler->selectButton('Upload!')->form();
			// $form['email'] = 'aonprogrammer@gmail.com';
			// $form['password'] = 'Tuksboy911';
			$form['upfile']->upload($track->file->getPath());
			// submit that form
			// dd($form);
			$crawler = $client->submit($form);
			$link = $crawler->filter('div.col-sm-8 td a')->first()->attr('href');
			dd($link);
		}
	});
// dd($crawler);
	return redirect()->route('frontend.index')->withFlashSuccess("File Uploaded");
});

/**
 * Global Routes
 * Routes that are used between both frontend and backend.
 */

// Switch between the included languages
Route::get('lang/{lang}', 'LanguageController@swap');

Route::get('/categories-sitemap', 'SitemapsController@categories')->name('categories-sitemap');
Route::get('/albums-sitemap', 'SitemapsController@albums')->name('albums-sitemap');
Route::get('/tracks-sitemap', 'SitemapsController@tracks')->name('tracks-sitemap');
Route::get('/sitemap', 'SitemapsController@show')->name('sitemap');


/* ----------------------------------------------------------------------- */

/*
 * Backend Routes
 * Namespaces indicate folder structure
 */
Route::group(['namespace' => 'Backend', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    /*
     * These routes need view-backend permission
     * (good if you want to allow more than one group in the backend,
     * then limit the backend features by different roles or permissions)
     *
     * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
     */
    includeRouteFiles(__DIR__.'/Backend/');
});

/* ----------------------------------------------------------------------- */

/*
 * Frontend Routes
 * Namespaces indicate folder structure
 */
Route::group(['namespace' => 'Frontend', 'as' => 'frontend.'], function () {
    includeRouteFiles(__DIR__.'/Frontend/');
});
