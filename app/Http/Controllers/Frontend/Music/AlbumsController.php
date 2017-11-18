<?php

namespace App\Http\Controllers\Frontend\Music;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Music\AlbumRepository;
use App\Repositories\Backend\Music\CacheRepository;
use SEOMeta;
use OpenGraph;
use Twitter;

class AlbumsController extends Controller
{
    protected $albums;

    public function __construct(AlbumRepository $albums, CacheRepository $cache)
    {
        $this->albums = $albums;
        $this->cache = $cache;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Albums Index
        $title = 'South African, Nigerian and American MP3 Full Albums';

        $albums = $this->albums()->paginate(10);
                
        $description = 'Download Full South African, Nigerian and American MP3 Full Albums. Download Album Tracks Individually or Zipped Free at NambaNamba.COM';
        $url = route('frontend.music.albums.index');

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->setCanonical($url)
                ->addKeyword(['south african hip hop mp3 albums downloads', 'nigerian hip hop zip albums download', 
                    'american hip hop mp3 albums download', 'south african house mp3 downloads', 'mzansi hip hop mp3 albums downloads'])
                ->addMeta('robots', 'noindex,follow');

        OpenGraph::setDescription($description)
                    ->setTitle($title)
                    ->setUrl($url)
                    ->addProperty('type', 'music.albums');

        Twitter::setTitle($title)
                ->setSite('@NambaNamba_Downloads');

        return view('frontend.music.albums.index', compact('title', 'albums', 'description'));
    }

    public function getAlbums(Category $category = null, Genre $genre = null, $take = -1, $pagination = 10)
    {
        if (!is_null($category) && !is_null($genre)) {
            $albums = $this->albums->query()->byCategoryAndGenre($category, $genre);
        } elseif (!is_null($category)) {
            $albums = $this->albums->query()->byCategory($category);
        } elseif (!is_null($genre)) {
            $albums = $this->albums->query()->byGenre($genre);
        } else {
            $albums = $this->albums->query()->get();
        }
        $albums = $albums->with('artists', 'category', 'genre')
                ->has('tracks')
                ->withCount('tracks')
                ->latest()
                ->take($take)
                ->paginate($pagination);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($album, $slug)
    {
        $album = $this->cache->findOrMake('albums', $album);

        $albums = $this->albums()->get();

        $related = $albums->filter(function($a) use ($album) {
            if ($a->id == $album->id || !$a->hasMedia('cover')) {
                return false;
            } elseif ($album->category_id == $a->category_id)  {
                return true;
            } elseif ($album->genre_id  == $a->genre_id) {
                return true;
            }
            return false;
        });

        if ($related->isNotEmpty() && $related->count() > 8) {
            $related = $related->random(4);
        }

        // dd($album->tracks->toArray());

        if ($album->tracks->isEmpty()) {
            return redirect()->route('frontend.index')->withFlashDanger("Album does not exist or has no tracks yet");
        }

        // dd($album->getFirstMedia('file'));

        $title = "Download {$album->full_title}";
        $url = route('frontend.music.albums.show', [$album->category, $album->genre, $album, $album->slug]);
        $description = "{$album->artists_title_comma} comes to you with the album titled {$album->title} under 
                        {$album->category->name} {$album->genre->name} 
                         Download and Stream this joint here and don't forget to share on social medias with friends...";
        $cover = $album->cover ? $album->cover->getFullUrl() : '';

        $seo_titled = str_ireplace('-', ' ', $album->full_title);

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($album->description ?: $description)
                ->addMeta('music.album:published_time', $album->created_at->toW3CString(), 'property')
                ->addMeta('music.album:section', $album->category->name, 'property')
                ->addKeyword(["{$seo_titled}  album download", $seo_titled, "{$seo_titled}  full album download", "Free {$album->category->name} {$album->genre->name} albums downloads", "{$album->full_title} zip download", "play all songs from {$album->full_title} free"]);

        OpenGraph::setDescription($album->description)
                    ->setTitle($title)
                    ->setUrl($url)
                    ->addProperty('type', 'music.album')
                    ->addProperty('locale', 'en-za')
                    ->addImage($cover);

        OpenGraph::setType('music.album')
            ->setMusicAlbum([
                'song:track' => $album->tracks->count(),
                'musician' => $album->artists_title_comma,
                'release_date' => $album->release_date
            ]);

        return view('frontend.music.albums.show', compact('title', 'cover', 'album', 'related', 'description'));
    }

    public function albums()
    {
        return $this->albums->query()
                    ->with('artists', 'category', 'genre', 'media')
                    ->has('tracks')
                    ->withCount('tracks')
                    ->latest();
    }
}
