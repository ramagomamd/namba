<?php

namespace App\Http\Controllers\Frontend\Music;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Music\TrackRepository;
use App\Models\Music\Track\Track;
use App\Repositories\Backend\Music\CacheRepository;
use App\Models\Music\Download\Download;
use SEOMeta;
use OpenGraph;
use Twitter;

class TracksController extends Controller
{
    protected $tracks;
    protected $cache;

    public function __construct(TrackRepository $tracks, CacheRepository $cache)
    {
        $this->tracks = $tracks;
        $this->cache = $cache;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Download All MP3 Tracks';

        $tracks = $this->tracks->query()
                ->whereNotNull('trackable_id')
                ->latest()
                ->paginate(20);

        $index = 0;
        foreach ($tracks as $track) {
            $track->index = $index;
            $index++;
        }
        // dd($tracks);

        if (request()->wantsJson()) {
            return $tracks;
        }

        $description = 'Download All South African, Nigerian and American MP3 Songs. Download and Stream All Songs Free at NambaNamba.COM';
        $url = route('frontend.music.tracks.index');

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

        return view('frontend.music.tracks.index', compact('title', 'tracks', 'description'));
    }

    public function showForAlbum($albumSlug, $track, $slug)
    {
        return $this->show($track);
    }

    public function showForSingle($track, $slug)
    {
        return $this->show($track);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($track)
    {
        $track = $this->cache->findOrMake('tracks', $track);
        // dd($track->toSearchableArray());
        // dd($track->file->getFullUrl());

        if (!is_null($track->trackable) && $track->trackable_type == 'albums') {
            $tracks = Track::with('artists', 'trackable.category', 'trackable.genre', 'media')
                    ->whereNotNull('trackable_id')
                    ->where('trackable_id', $track->trackable_id)
                    // ->orWhereHas('artists', function ($query) use ($track) {
                    //     $artists = $track->artists->pluck('id');
                    //     return $query->whereIn('id', $artists));
                    // })
                    ->latest()
                    ->get()
                    ->reject(function($query) use ($track) {
                        return $track->id == $query->id;
                    });
        } else {
            $tracks = Track::with('artists', 'trackable.category', 'trackable.genre', 'media')
                    ->whereNotNull('trackable_id')
                    ->where('trackable_type', 'singles')
                    // ->orWhereHas('artists', function ($query) use ($track) {
                    //     $artists = $track->artists->pluck('id');
                    //     return $query->whereIn('id', $artists);
                    // })
                    ->latest()
                    ->get()
                    ->reject(function($query) use ($track) {
                        return is_null($query->trackable) || ($track->id == $query->id);
                    })
                    ->filter(function($query) use ($track) {
                        return $query->trackable->category->id == $track->trackable->category->id;
                    });
        }

        if ($tracks->isNotEmpty() && $tracks->count() > 8) {
            $tracks = $tracks->random(8);
        }

        $title = "Download {$track->full_title} " . strtoupper($track->file->extension);
        $url = $track->frontend_show_route;
        $album = $track->trackable->fuller_title ?: 
                        "{$track->trackable->category->name} {$track->trackable->genre->name} singles";
        $description = "{$track->artists_title_comma} comes to you with a song titled {$track->title} under {$album}
                         Download and Stream this mp3 song here. Enjoy {$track->file->getFullUrl()}";
        $cover = $track->cover ? $track->cover->getFullUrl() : '';

        $seo_titled = str_ireplace('-', ' ', $track->full_title);

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($track->description ?: $description)
                ->addMeta('music.album:published_time', $track->created_at->toW3CString(), 'property')
                ->addMeta('music.album:section', $track->trackable->category->name, 'property')
                ->addKeyword([$seo_titled, "{$seo_titled} mp3", "{$seo_titled} download song", "download and stream {$seo_titled}", 
                            "play {$track->full_title} free at NambaNamba.COM"]);

        OpenGraph::setDescription($track->description)
                    ->setTitle($title)
                    ->setUrl($url)
                    ->addProperty('type', 'music.song')
                    ->addProperty('locale', 'en-za')
                    ->addImage($cover);

        OpenGraph::setType('music.song')
            ->setMusicSong([
                'duration' => $track->duration,
                'album' => $album,
                'musician' => $track->all_artists->pluck("name")
            ]);

        return view('frontend.music.tracks.show', compact('title', 'cover', 'track', 'tracks', 'description'));
    }

    public function download(Track $track)
    {
        $this->increment($track->id);
        $filename = $track->full_title . '.' . $track->file->getExtensionAttribute();

        return response()->download($track->path, $filename);
    }

    public function increment($trackId)
    {
        $download = new Download;
        $download->downloadable_id = $trackId;
        $download->downloadable_type = "tracks";

        $download->save();

        return "Done!";
    }
}
