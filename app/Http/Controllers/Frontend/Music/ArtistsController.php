<?php

namespace App\Http\Controllers\Frontend\Music;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Music\ArtistRepository;
use App\Repositories\Backend\Music\TrackRepository;
use App\Models\Music\Artist\Artist;
use SEOMeta;
use OpenGraph;
use Twitter;

class ArtistsController extends Controller
{
    protected $artists;
    protected $tracks;

    public function __construct(ArtistRepository $artists, TrackRepository $tracks)
    {
        $this->artists = $artists;
        $this->tracks = $tracks;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "All Artists";
        $artists = $this->artists
                        ->query()
                        ->orderBy('name')
                        ->withCount(['albums', 'singles', 'tracks'])
                        ->paginate(20);

        $description = "All Artists At NambaNamba Downloads. View Each Artist Music Albums and Singles MP3 Downloads at their Pages";
        $url = route('frontend.music.artists.index');

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->setCanonical($url);

        OpenGraph::setDescription($description)
                ->setTitle($title)
                ->setUrl($url)
                ->addProperty('type', 'music.artists');

        Twitter::setTitle($title)
                ->setSite('@NambaNamba_Downloads');

        return view('frontend.music.artists.index', compact('title', 'artists', 'description'));
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Artist $artist)
    {  
        $albums = $artist->albums()
                ->with('artists', 'category', 'genre')
                ->has('tracks')
                ->withCount('tracks');
        $albums_count = $albums->count();
        $albums = $albums->latest()->take(5)->get();

        $singles = $artist->singles()
                    ->has('track')
                    ->with('track');
        $singles_count = $singles->count();
        $singles = $singles->latest()->take(5)->get();

        $tracks = $artist->tracks()
                        ->whereNotNull('trackable_id')
                        ->wherePivotIn('role', ['feature', 'producer']);

        $tracks_count = $tracks->count();
        $tracks = $tracks->take(6)->get();
        $index = 0;
        foreach ($tracks as $track) {
            $track->index = $index;
            $index++;
        }

        $title = "Download {$artist->name} Albums and Singles";
        $url = route('frontend.music.artists.show', $artist);
        $cover = $artist->cover ? $artist->cover->getFullUrl() : '';

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($artist->bio)
                ->addMeta('music.album:published_time', $artist->created_at->toW3CString(), 'property')
                ->addKeyword([
                            "{$artist->name} songs downloads and streaming", 
                            "download or stream {$artist->name} mp3s here", 
                            "stream {$artist->name} full albums and singles from NambaNamba.COM"
                ]);

        OpenGraph::setDescription($artist->description)
                ->setTitle($title)
                ->setUrl($url)
                ->addProperty('type', 'music.artist')
                ->addProperty('locale', 'en-za')
                ->addImage($cover);

        return view('frontend.music.artists.show', 
                compact('title', 'artist', 'albums', 'singles', 'tracks', 
                    'albums_count', 'singles_count', 'tracks_count', 'description'));
    }

    public function albums(Artist $artist)
    {
        $title = "All {$artist->name} Music Albums";
        $albums = $artist->albums()
                    ->with('artists', 'category', 'genre')
                    ->has('tracks')
                    ->withCount('tracks')
                    ->latest()
                    ->paginate(10); 


        $description = "Download All {$artist->name} MP3 Music Albums. Download Album Songs Individually or Download Full Zipped Album Free at NambaNamba.COM";
        $url = route('frontend.music.artists.albums', $artist);

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

        return  view('frontend.music.albums.index', compact('title', 'albums', 'artist', 'description'));
    }

    public function singles(Artist $artist)
    {
        $title = "All {$artist->name} Music Singles";
        $singles = $artist->singles()
                    ->has('track')
                    ->with('track')
                    ->latest()
                    ->paginate(10);

        $description = "Download All {$artist->name} MP3 Music Singles. Download Single Songs at NambaNamba.COM";
        $url = route('frontend.music.artists.singles', $artist);

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

        return  view('frontend.music.singles.index', compact('title', 'singles', 'artist', 'description'));
    }

    public function tracks(Artist $artist)
    {
        $title = "All {$artist->name} Tracks";
        $tracks = $tracks = $artist->tracks()
                ->wherePivotIn('role', ['feature', 'producer'])
                ->whereNotNull('trackable_id')
                // ->distinct('slug')
                ->latest()
                ->paginate(20);
                // dd($tracks);
        $index = 0;
        foreach ($tracks as $track) {
            $track->index = $index;
            $index++;
        }

        $description = "Download All {$artist->name} MP3 Songs. Download and Stream All Songs By {$artist->name } at NambaNamba.COM";
        $url = route('frontend.music.artists.tracks', $artist);

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

        return  view('frontend.music.tracks.index', compact('title', 'tracks', 'artist', 'description'));
    }
}
