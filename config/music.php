<?php

use App\Models\Music\Category\Category;
use App\Models\Music\Album\Album;
use App\Models\Music\Artist\Artist;
use App\Models\Music\Track\Track;
use App\Models\Music\Track\Cover;
use App\Models\Music\Genre\Genre;
use App\Models\Music\Single\Single;
use App\Models\Music\Download\Download;
use App\Models\Music\Link\Link;

return [

	'category' => [
			'model' => Category::class,
			'table' => 'categories',
	],

	'album' => [
			'model' => Album::class,
			'table' => 'albums',
	],

	'artist' => [
			'model' => Artist::class,
			'table' => 'artists',
	],

	'track' => [
			'model' => Track::class,
			'table' => 'tracks',
	],

	'link' => [
			'model' => Link::class,
			'table' => 'links',
	],

	'download' => [
			'model' => Download::class,
			'table' => 'downloads',
	],

	'genre' => [
			'model' => Genre::class,
			'table' => 'genres',
	],

	'single' => [
			'model' => Single::class,
			'table' => 'singles',
	],
	
];