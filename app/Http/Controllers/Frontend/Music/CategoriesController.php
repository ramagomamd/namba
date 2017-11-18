<?php

namespace App\Http\Controllers\Frontend\Music;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Music\CategoryRepository;
use App\Models\Music\Category\Category;
use App\Repositories\Backend\Music\AlbumRepository;
use App\Repositories\Backend\Music\SingleRepository;
use App\Repositories\Backend\Music\CacheRepository;
use SEOMeta;
use OpenGraph;
use Twitter;

class CategoriesController extends Controller
{
    protected $categories;
    protected $albums;
    protected $singles;
    protected $cache;

    public function __construct(CategoryRepository $categories, AlbumRepository $albums, 
                                SingleRepository $singles, CacheRepository $cache)
    {
        $this->categories = $categories;
        $this->albums = $albums;
        $this->singles = $singles;
        $this->cache = $cache;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category = $category->load('genres');

        $genres = $category->genres->map(function ($genre) use ($category) {
            $albums = $this->albums->query()->byCategoryAndGenre($category, $genre);
            $singles = $this->singles->query()->byCategoryAndGenre($category, $genre);
            if ($albums->exists() || $singles->exists()) {
                return $genre;
            }
            return null;
        })->reject(null);

        $albums = $this->cache->findOrMake('albums')
                        ->where('category_id', $category->id)
                        ->take(5);

        $singles = $this->cache->findOrMake('singles')
                        ->where('category_id', $category->id)
                        ->take(5);

        $title = "Download {$category->name} Albums and Singles";
        $description = $category->description ?? $title;
        $url = route('frontend.music.categories.show', $category);

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->addKeyword([
                            "Free {$category->name} songs downloads and streaming", 
                            "download or stream {$category->name} mp3s here", 
                            "stream {$category->name} full albums and singles from NambaNamba.COM"
                ])
                ->addMeta('robots', 'noindex,follow');

        OpenGraph::setDescription($description)
                ->setTitle($title)
                ->setUrl($url)
                ->addProperty('locale', 'en-za');

        return view('frontend.music.categories.show', compact(
                    'title', 'category', 'description',
                    'genres', 'albums', 'singles'
        ));
    }

    public function albums(Category $category)
    {
        $title = "All {$category->name} Albums Downloads";

        $albums = $this->albums->query()
                ->with('artists', 'category', 'genre')
                ->has('tracks')
                ->byCategory($category)  
                ->withCount('tracks')
                ->latest()->paginate(10); 

        $description = "Stream and Download All {$category->name} MP3 Music Albums. Download Album Songs Individually or Download Full Zipped Album Free at NambaNamba.COM";
        $url = route('frontend.music.categories.albums', $category);

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->setCanonical($url)
                ->addMeta('robots', 'noindex,follow');

        OpenGraph::setDescription($description)
                ->setTitle($title)
                ->setUrl($url)
                ->addProperty('type', 'music.albums');

        Twitter::setTitle($title)
                ->setSite('@NambaNamba_Downloads');

        return  view('frontend.music.albums.index', compact('title', 'albums', 'category', 'description'));
    }

    public function singles(Category $category)
    {
        $title = "All {$category->name} Singles Downloads";

        $singles = $this->singles->query()
                    ->byCategory($category) 
                    ->has('track')
                    ->with('track')
                    ->latest()->paginate(10);

       $description = "Stream and Download All {$category->name} MP3 Music Singles. Download Single Songs Free at NambaNamba.COM";
        $url = route('frontend.music.categories.singles', $category);

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->setCanonical($url)
                ->addMeta('robots', 'noindex,follow');

        OpenGraph::setDescription($description)
                ->setTitle($title)
                ->setUrl($url)
                ->addProperty('type', 'music.songs');

        Twitter::setTitle($title)
                ->setSite('@NambaNamba_Downloads');

        return  view('frontend.music.singles.index', compact('title', 'singles', 'category', 'description'));
    }

    public function getAlbums()
    {
        return $this->albums->query()
                    ->with('artists', 'category', 'genre', 'media')
                    ->has('tracks')
                    ->withCount('tracks')
                    ->latest();
    }

    public function getSingles()
    {
        return $this->singles->query()
                    ->has('track')
                    ->with('track')
                    ->latest();
    }
}
