<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Music\CategoryRepository;
use App\Repositories\Backend\Music\GenreRepository;
use App\Repositories\Backend\Music\AlbumRepository;
use App\Repositories\Backend\Music\SingleRepository;
use App\Repositories\Backend\Music\CacheRepository;
use SEOMeta;
use OpenGraph;
use Twitter;

/**
 * Class FrontendController.
 */
class FrontendController extends Controller
{
    protected $categories;
    protected $albums;
    protected $singles;
    protected $genres;
    protected $cache;

    public function __construct(CategoryRepository $categories, AlbumRepository $albums, SingleRepository $singles,
                                GenreRepository $genres, CacheRepository $cache)
    {
        $this->categories = $categories;
        $this->albums = $albums;
        $this->singles = $singles;
        $this->genres = $genres;
        $this->cache = $cache;
    }
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {

        // $albums = $this->getAlbums();
        $albums = $this->cache->findOrMake('albums');

        $latestAlbums = $albums->take(5);

        $mzansiAlbums = $albums->filter(function($album) {
            return $album->category_id == 1;
        })->reject(function($album) use ($latestAlbums) {
            return $latestAlbums->contains($album);
        })->take(5);

        $internationalAlbums = $albums->filter(function($album) {
            return $album->category_id == 2;
        })->reject(function($album) use ($latestAlbums) {
            return $latestAlbums->contains($album);
        })->take(5);

        $nigerianAlbums = $albums->filter(function($album) {
            return $album->category_id == 3;
        })->reject(function($album) use ($latestAlbums) {
            return $latestAlbums->contains($album);
        })->take(5);

        // $mzansiAlbums = $this->getAlbums()->where('category_id', 1)->skip(3)->take(5)->get();
        // $internationalAlbums = $this->getAlbums()->where('category_id', 2)->skip(3)->take(5)->get();
        // $nigerianAlbums = $this->getAlbums()->where('category_id', 3)->skip(3)->take(5)->get();

        // $singles = $this->getSingles();
        $singles = $this->cache->findOrMake('singles');
        
        $latestSingles = $singles->take(5);

        $mzansiSingles = $singles->filter(function($single) {
            return $single->category_id == 1;
        })->reject(function($album) use ($latestSingles) {
            return $latestSingles->contains($album);
        })->take(5);

        $internationalSingles = $singles->filter(function($single) {
            return $single->category_id == 2;
        })->reject(function($album) use ($latestSingles) {
            return $latestSingles->contains($album);
        })->take(5);

        $nigerianSingles = $singles->filter(function($single) {
            return $single->category_id == 3;
        })->reject(function($album) use ($latestSingles) {
            return $latestSingles->contains($album);
        })->take(5);

        /*$mzansiSingles = $this->getSingles()->where('category_id', 1)->skip(3)->take(5)->get();
        $internationalSingles = $this->getSingles()->where('category_id', 2)->skip(3)->take(5)->get();
        $nigerianSingles = $this->getSingles()->where('category_id', 3)->skip(3)->take(5)->get();*/

        $title = title_case("download south african, nigerian and american mp3 music downloads");
        $description = 'Download South African, American and Nigerian Hip Hop MP3 Music. NambaNamba Downloads let you download your favorite singles and albums in high quality and lightning speed';
        $url = route('frontend.index');

        // SEO Tags
        SEOMeta::setTitle($title)
                ->addKeyword([
                        "Free south african songs downloads and streaming", 
                        "download or stream international mp3s here", 
                        "stream mzansi full albums and singles from NambaNamba.COM"
                    ])
                ->setDescription($description)
                ->addMeta('robots', 'noindex,follow');

        OpenGraph::setTitle($title)
                ->setDescription($description)
                ->setUrl($url)
                ->addProperty('locale', 'en-za');

        Twitter::setTitle($title)
                ->setSite('@NambaNamba_Downloads');

        return view('frontend.index', [
                    'title' => $title,
                    'description' => $description,
                    'latestAlbums' => $latestAlbums,
                    'mzansiAlbums' => $mzansiAlbums,
                    'internationalAlbums' => $internationalAlbums,
                    'nigerianAlbums' => $nigerianAlbums,
                    'latestSingles' => $latestSingles,
                    'mzansiSingles' => $mzansiSingles,
                    'internationalSingles' => $internationalSingles,
                    'nigerianSingles' => $nigerianSingles,
        ]);
    }

    public function getAlbums()
    {
        return $this->albums->query()
                    ->with('artists', 'category', 'genre', 'media')
                    ->has('tracks')
                    ->withCount('tracks')
                    ->latest()
                    ->get();
    }

    public function getSingles()
    {
        return $singles = $this->singles->query()
                    ->has('track')
                    ->with('track')
                    ->latest()
                    ->get();
    }

    public function displayAlbums()
    {
        $albums = $this->albums->query()
                    ->with('artists', 'media')
                    ->has('tracks')
                    ->where('category_id', 1)
                    ->oldest()
                    ->paginate(20);

        return view('frontend.all-albums', compact('albums'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function macros()
    {
        return view('frontend.macros');
    }
}
