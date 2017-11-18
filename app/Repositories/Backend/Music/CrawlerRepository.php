<?php

namespace App\Repositories\Backend\Music;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CrawlerRepository
{
	protected $singles;
	protected $albums;

	public function __construct(SingleRepository $singles, AlbumRepository $albums)
	{
		$this->singles = $singles;
		$this->albums = $albums;
	}

	public function getSingles(array $status = null)
	{
		$singles = DB::table('singles_crawler')
						->join('tracks_crawler', 'crawlable_id', '=', 'singles_crawler.id')
						->where('crawlable_type', '=', 'singles')
						->select('singles_crawler.*', 'tracks_crawler.*')
						->latest('singles_crawler.id');

		if (!is_null($status)) {
			$singles = $singles->whereIn('singles_crawler.status', $status);
		}

		return $singles;
	}

	public function crawlSingles()
	{
		$singles = $this->getSingles(['uncrawled'])->get();

		$singles->each(function($single) {
			$this->singles->crawl((array) $single);
		});

		return;
	}

	public function getAlbums(array $status = null)
	{
		$albums = DB::table('albums_crawler')
						->latest('id');

		if (!is_null($status)) {
			$albums = $albums->whereIn('status', $status);
		}

		return $albums;
	}

	public function crawlAlbums()
	{
		$albums = $this->getAlbums(['uncrawled'])->get();
					// dd($albums);
		$albums->each(function($album) {
			$this->albums->crawl((array) $album);
		});

		return;
	}
}