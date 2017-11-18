<?php

namespace App\Http\Controllers\Backend\Music;

use App\Http\Controllers\Controller;
use App\Services\Music\Crawlers\Crawler;
use App\Repositories\Backend\Music\SingleRepository;
use App\Repositories\Backend\Music\AlbumRepository;
use App\Repositories\Backend\Music\CrawlerRepository;

class CrawlersController extends Controller
{
	protected $crawler;
	protected $singles;
	protected $albums;
	protected $repo;

	public function __construct(Crawler $crawler, SingleRepository $singles, AlbumRepository $albums, 
								CrawlerRepository $repo)
	{
		$this->crawler = $crawler;
		$this->singles = $singles;
		$this->albums = $albums;
		$this->repo = $repo;
	}

	public function index()
	{
		$title = "Music Crawler";

		$singles = $this->repo->getSingles();
		$albums = $this->repo->getAlbums();

		// dd($singles);
		// Show All Crawlables, Albums; Singles & Tracks 
		// Distinguish between crawled and uncrawled
		return view('backend.music.crawl.index', [
						'title' => $title, 
						'singles' => $singles->take(5)->get(), 
						'singles_count' => $singles->count(),
						'albums'=> $albums->take(5)->get(),
						'albums_count' => $albums->count()
				]);
	}

	public function getSingles()
	{
		$title = "Single Crawls";

		$singles = $this->repo->getSingles()->paginate(20);
		// dd($singles);

		return view('backend.music.crawl.singles', compact('title', 'singles'));
	}

	public function crawlSingles()
	{
		$this->repo->crawlSingles();

		return back()->withFlashInfo("Finished Crawling Singles");
	}

	public function getAlbums()
	{
		$title = "Albums Crawls";

		$albums = $this->repo->getAlbums()->paginate(20);
		// dd($albums);

		return view('backend.music.crawl.albums', compact('title', 'albums'));
	}

	public function crawlAlbums()
	{
		$this->repo->crawlAlbums(['uncrawled']);

		return back()->withFlashInfo("Finished Crawling Albums");
	}

	public function crawl()
	{
		$results = $this->crawler->process(request()->all());

		return back()->withFlashInfo("Done Scanning Sites");
	}
}