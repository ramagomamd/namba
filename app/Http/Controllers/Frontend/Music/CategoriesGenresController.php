<?php

namespace App\Http\Controllers\Frontend\Music;

use App\Http\Controllers\Controller;
use App\Models\Music\Category\Category;
use App\Models\Music\Genre\Genre;
use App\Repositories\Backend\Music\AlbumRepository;
use App\Repositories\Backend\Music\SingleRepository;
use App\Repositories\Backend\Music\CacheRepository;
use SEOMeta;
use OpenGraph;
use Twitter;

class CategoriesGenresController extends Controller
{
    protected $albums;
    protected $singles;
    protected $cache;

    public function __construct(AlbumRepository $albums, SingleRepository $singles, CacheRepository $cache)
    {
        $this->albums = $albums;
        $this->singles = $singles;
        $this->cache = $cache;
    }

    public function index(Category $category, Genre $genre)      
    {
        $albums = $this->cache->findOrMake('albums')
                        ->where('category_id', $category->id)
                        ->where('genre_id', $genre->id)
                        ->take(5);

        $singles = $this->cache->findOrMake('singles')
                        ->where('category_id', $category->id)
                        ->where('genre_id', $genre->id)
                        ->take(5);

        $title = "Download {$category->name} {$genre->name} Albums and Singles";
        $description = "Download All {$category->name} {$genre->name} MP3 Music Albums and Singles. Download Albums and Singles Songs Individually or Download Full Zipped Album Free at NambaNamba.COM";
        $url = route('frontend.music.categories.genres', [$category, $genre]);

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->addKeyword([
                        "{$category->name} {$genre->name} songs downloads and streaming", 
                        "download or stream {$category->name} mp3s here", 
                        "stream {$category->name} {$genre->name} full albums and singles from NambaNamba.COM"
                    ])
                ->addMeta('robots', 'noindex,follow');

        OpenGraph::setDescription($description)
                ->setTitle($title)
                ->setUrl($url)
                ->addProperty('locale', 'en-za');

        return view('frontend.music.general', compact('title', 'category', 'genre', 'albums', 'singles', 'description'));
    }

    public function getAlbums(Category $category, Genre $genre)
    {
        $title = "{$category->name} {$genre->name} Albums Downloads";

        $albums = $this->albums->query()
                    ->with('artists', 'category', 'genre')
                    ->has('tracks')
                    ->byCategoryAndGenre($category, $genre)
                    ->withCount('tracks')
                    ->latest()->paginate(10);

        $description = "Download All {$category->name} {$genre->name} MP3 Music Albums. Download Album Songs Individually or Download Full Zipped Album Free at NambaNamba.COM";
        $url = route('frontend.music.categories.genres.albums', [$category, $genre]);

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->setCanonical($url);

        OpenGraph::setDescription($description)
                ->setTitle($title)
                ->setUrl($url)
                ->addProperty('type', 'music.albums');

        Twitter::setTitle($title)
                ->setSite('@NambaNamba_Downloads');

        return  view('frontend.music.albums.index', compact('title', 'albums', 'category', 'genre', 'description'));
    }

    public function getSingles(Category $category, Genre $genre)
    {
        $title = "{$category->name} {$genre->name} Singles Downloads";

        $singles = $this->singles->query()
                    ->byCategoryAndGenre($category, $genre)
                    ->with('track')
                    ->latest()->paginate(10);

        $description = "Download All {$category->name} {$genre->name} MP3 Music Singles. Download Single Songs Free at NambaNamba.COM";
        $url = route('frontend.music.categories.genres.singles', [$category, $genre]);

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->setCanonical($url);

        OpenGraph::setDescription($description)
                ->setTitle($title)
                ->setUrl($url)
               ->addProperty('type', 'music.songs');

        Twitter::setTitle($title)
                ->setSite('@NambaNamba_Downloads');

        return view('frontend.music.singles.index', compact('title', 'singles', 'category', 'genre', 'description'));
    }

    public function albums()
    {
        return $this->albums->query()
                    ->with('artists', 'category', 'genre', 'media')
                    ->has('tracks')
                    ->withCount('tracks')
                    ->latest();
    }

    public function singles()
    {
        return $this->singles->query()
                    ->has('track')
                    ->with('track')
                    ->latest();
    }
}
