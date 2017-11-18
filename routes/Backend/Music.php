<?php

Route::group([
	'prefix' => 'music',
	'as' => 'music.',
	'namespace' => 'Music',
	'middleware' => 'admin'
], function() {
	Route::get('crawl', 'CrawlersController@index')->name('crawl.index');
	Route::post('crawl', 'CrawlersController@crawl')->name('crawl');
	Route::get('crawl/albums', 'CrawlersController@getAlbums')->name('crawl.albums');
	Route::get('crawl/singles', 'CrawlersController@getSingles')->name('crawl.singles');
	Route::post('crawl/albums', 'CrawlersController@crawlAlbums')->name('crawl.albums');
	Route::post('crawl/singles', 'CrawlersController@crawlSingles')->name('crawl.singles');
	
	Route::resource('albums', 'AlbumsController', ['except' => ['create', 'show']]);
	Route::get('albums/refresh-cache/{album?}', 'AlbumsController@refreshCache')->name('albums.refresh-cache');
	Route::post('albums/upload', 'AlbumsController@upload')->name('albums.upload');
	Route::post('albums/bulk-actions', 'AlbumsController@bulkActions')->name('albums.bulk-actions');
	Route::post('albums/bulk-update', 'AlbumsController@bulkUpdate')->name('albums.bulk-update');
	Route::post('albums/bulk-delete', 'AlbumsController@bulkDelete')->name('albums.bulk-delete');
	Route::post('albums/upload-zip', 'AlbumsController@storeZip')->name('albums.upload-zip');
	Route::post('albums/remote-upload', 'AlbumsController@remoteUpload')->name('albums.remote-upload');
	Route::post('albums/remote-upload-tracks', 'AlbumsController@remoteUploadTracks')->name('albums.remote-upload-tracks');
	Route::post('albums/upload-cover', 'AlbumsController@storeCover')->name('albums.upload-cover');
	Route::get('albums/bulk-generate-zip', 'AlbumsController@bulkGenerateArchive')->name('albums.bulk-generate-zip');
	Route::get('albums/{album}/generate-zip', 'AlbumsController@generateArchive')->name('albums.generate-zip');
	Route::post('albums/{album}/download', 'AlbumsController@download')->name('albums.download');

	Route::resource('artists', 'ArtistsController', ['except' => ['create', 'edit']]);
	Route::get('artists/{artist}/albums', 'ArtistsController@albums')->name('artists.albums');
	Route::get('artists/{artist}/singles', 'ArtistsController@singles')->name('artists.singles');
	Route::get('artists/{artist}/tracks', 'ArtistsController@tracks')->name('artists.tracks');

	Route::get('categories/refresh-cache', 'CategoriesController@refreshCache')->name('categories.refresh-cache');
	Route::resource('categories', 'CategoriesController', ['except' => ['create', 'edit']]);
	Route::get('categories/{category}/albums', 'CategoriesController@albums')
		->name('categories.albums');
	Route::get('categories/{category}/singles', 'CategoriesController@singles')
		->name('categories.singles');
	Route::get('categories/{category}/{genre}', 'CategoriesGenresController@index')
		->name('categories.genres');
	Route::get('categories/{category}/{genre}/albums/{album}/{slug}', 'AlbumsController@show')
		->name('albums.show');
	Route::get('categories/{category}/{genre}/albums', 'CategoriesGenresController@getAlbums')
		->name('categories.genres.albums');
	Route::get('categories/{category}/{genre}/singles', 'CategoriesGenresController@getSingles')
		->name('categories.genres.singles');
	Route::get('categories/{category}/{genre}/albums/{album}/{albumSlug}/{track}/{slug}', 'TracksController@showForAlbum')
		->name('albums.tracks.show');
	Route::get('categories/{category}/{genre}/singles/{track}/{slug}', 'TracksController@showForSingle')
		->name('singles.tracks.show');
	Route::delete('categories/{category}/{genre}/delete', 'CategoriesController@deleteGenre')
		->name('categories.genres.remove');

	Route::get('genres/refresh-cache', 'GenresController@refreshCache')->name('genres.refresh-cache');
	Route::resource('genres', 'GenresController', ['except' => ['create', 'edit']]);
	Route::get('genres/{genre}/singles', 'GenresController@singles')->name('genres.singles');
	Route::get('genres/{genre}/albums', 'GenresController@albums')->name('genres.albums');

	Route::resource('singles', 'SinglesController', ['except' => 'create', 'show']);
	Route::post('singles/remote-upload', 'SinglesController@remoteUpload')->name('singles.remote-upload');
	Route::post('singles/bulk-actions', 'SinglesController@bulkActions')->name('singles.bulk-actions');
	Route::post('singles/bulk-update', 'SinglesController@bulkUpdate')->name('singles.bulk-update');
	Route::post('singles/bulk-delete', 'SinglesController@bulkDelete')->name('singles.bulk-delete');

    Route::post('tracks/get', 'TracksController@get')->name('tracks.get');
	Route::resource('tracks', 'TracksController', ['except' => ['create', 'show', 'edit']]);
	Route::post('tracks/download/{track}', 'TracksController@download')->name('tracks.download');
	Route::get('tracks/refresh-cache/{track?}', 'TracksController@refreshCache')->name('tracks.refresh-cache');
	Route::post('tracks/bulk-actions', 'TracksController@bulkActions')->name('tracks.bulk-actions');
	Route::post('tracks/bulk-update', 'TracksController@bulkUpdate')->name('tracks.bulk-update');
	Route::post('tracks/bulk-delete', 'TracksController@bulkDelete')->name('tracks.bulk-delete');
});