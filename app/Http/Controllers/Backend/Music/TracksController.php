<?php

namespace App\Http\Controllers\Backend\Music;

use App\Models\Music\Track\Track;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Music\TrackRepository;
use App\Repositories\Backend\Music\ArtistRepository;
use App\Repositories\Backend\Music\GenreRepository;
use App\Http\Requests\Backend\Music\Track\ManageTrackRequest;
use App\Http\Requests\Backend\Music\Track\StoreTrackRequest;
use App\Http\Requests\Backend\Music\Track\UpdateTrackRequest;
use Illuminate\Validation\Rule;
use Download;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Backend\Music\CacheRepository;

class TracksController extends Controller
{
    protected $tracks;
    protected $artists;
    protected $genres;
    protected $cache;

    public function __construct(TrackRepository $tracks, ArtistRepository $artists, GenreRepository $genres,
                                CacheRepository $cache)
    {
        $this->tracks = $tracks;
        $this->artists = $artists;
        $this->genres = $genres;
        $this->cache = $cache;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ManageTrackRequest $request)
    {
        $title =  trans('labels.backend.music.tracks.all');
        $tracks = $this->tracks->query()
                ->whereNotNull('trackable_id')
                ->sortable(['id' => 'desc'])
                ->paginate();
// dd($tracks);
                // $user->sortable('name')->paginate(10);

        return view('backend.music.tracks.index', compact('title', 'tracks'));
    }

    /**
     * Display the Track.
     *
     * @param  int  $track
     * @return \Illuminate\Http\Response
     */
    public function showForAlbum($category, $genre, $album, $albumSlug, Track $track, $slug, ManageTrackRequest $request)
    {
        // dd($track->file->getFullUrl());
        $title = trans('labels.backend.music.tracks.management');
        $artists = $this->artists->query()->pluck('name', 'name');
        $genres = $this->genres->query()->pluck('name', 'name');

        return view('backend.music.tracks.show', compact('title', 'track', 'artists', 'genres'));
    }

    /**
     * Display the Track.
     *
     * @param  int  $track
     * @return \Illuminate\Http\Response
     */
    public function showForSingle($category, $genre, Track $track, $slug, ManageTrackRequest $request)
    {
        // dd($track->file->getFullUrl());
        $title = trans('labels.backend.music.tracks.management');
        $artists = $this->artists->query()->pluck('name', 'name');
        $genres = $this->genres->query()->pluck('name', 'name');

        return view('backend.music.tracks.show', compact('title', 'track', 'artists', 'genres'));
    }

    public function refreshCache($track = null, ManageTrackRequest $request)
    {
        if (!is_null($track)) {
            $this->cache->clear('tracks', $track);
            $track = $this->cache->findOrMake('tracks', $track);

            return back()->withFlashSuccess("Cache Successfully  Refreshed For {$track->full_title}");
        }

        Track::get()->each(function($track) {
            $this->cache->clear('tracks', $track->id);
            $this->cache->findOrMake('tracks', $track->id);
        });

        return back()->withFlashSuccess("All Track Cache Successfully  Refreshed");
    }

    public function bulkActions(ManageTrackRequest $request)
    {
        $this->validate($request, [
            'tracks' => 'required|array',
            'tracks.*' => 'exists:tracks,id',
            'action' => 'required|string|in:Edit,Delete'
        ]);
        $tracks = Track::whereIn('id', $request->tracks)->get();

        if ($request->action == 'Edit') {
            return view('backend.music.tracks.bulk-edit', compact('tracks'));
        } else {
            return view('backend.music.tracks.bulk-delete', compact('tracks'));
        }
        // dd($request->all());
    }

    public function bulkUpdate(ManageTrackRequest $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'tracks' => 'required|array',
            'tracks.' => 'exists:tracks,id'
        ]);
        $tracks = $request->get('tracks');

        foreach ($tracks as $id => $data) {
            $track =  Track::findOrFail($id);
            $this->tracks->updateTitle($track, $data['full_title']);
        }

        return redirect()->route('admin.music.tracks.index')->withFlashInfo("Done Editing Tracks");
    }

    public function bulkDelete(ManageTrackRequest $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'tracks' => 'required|array',
            'tracks.' => 'exists:tracks,id',
            'tracks.*.confirm' => 'required|string|in:yes,no'
        ]);
        $this->tracks->deleteBulk($request->get('tracks'));

        return redirect()->route('admin.music.tracks.index')->withFlashInfo("Done Deleting Tracks");
    }

    public function download(Track $track, ManageTrackRequest $request)
    {
        $filename = $track->full_title . '.' . $track->file->getExtensionAttribute();
        // $download = Download::fromTrack($track);

        return response()->download($track->path, $filename);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Track $track, UpdateTrackRequest $request)
    {
        $result = $this->tracks->update($track, $request->only(
                                'title', 'comment', 'cover', 'main', 'features', 'producer', 
                                'year', 'number', 'genres', 'copyright'));

        // dd($result);
        $this->cache->clear('tracks', $track->id);

        return redirect()->to($track->backendShowRoute)
                        ->withFlashSuccess(trans('alerts.backend.music.tracks.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Track $track, ManageTrackRequest $request)
    {
        $this->cache->clear('tracks', $track->id);
        $this->tracks->delete($track);

        return back()->withFlashSuccess(trans('alerts.backend.music.tracks.deleted'));
    }
}
