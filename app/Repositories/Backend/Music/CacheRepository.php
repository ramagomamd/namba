<?php

namespace App\Repositories\Backend\Music;

use Illuminate\Support\Facades\Cache;
use App\Repositories\Backend\SettingRepository;
use App\Models\Music\Album\Album;
use App\Models\Music\Category\Category;
use App\Models\Music\Single\Single;
use App\Models\Music\Genre\Genre;
use App\Models\Music\Track\Track;
use App\Models\Setting\Setting;
use Carbon\Carbon;

class CacheRepository
{
    public function findOrMake($type, $key = '')
    {
        switch ($type) {
            case 'albums':
                return $this->albums($key);
                break;
            case 'singles':
                return $this->singles();
                break;
            case 'settings':
                return $this->settings($key);
                break;
            case 'tracks':
                return $this->tracks($key);
                break;
            case 'categories':
                return $this->categories();
                break;
            case 'genres':
                return $this->genres();
                break;
            case 'all':
                return $this->all();
                break;
        }
    }

    private function albums($id = '')
    {
        if (!empty($id)) {
            return $this->cacheAlbum($id);
        } else {
            return $this->cacheLatestAlbums();
        }
    }

    public function cacheLatestAlbums()
    {
        $albums = $this->get('albums/latest');
        if (!is_null($albums)) return $albums;

        $albums = Album::with('artists', 'category', 'genre', 'media')
                    ->has('tracks')
                    ->withCount('tracks')
                    ->latest()
                    ->take(50)
                    ->get();

        return $this->put('albums/latest', $albums, 70);
    }

    private function cacheAlbum($id)
    {
        $album = $this->get('albums/' . $id);
        if (!is_null($album)) return $album;

        $album = Album::where('id', $id)->firstOrFail();
        $album = $album->load('tracks', 'category', 'genre',  'links');
        $index = 0;
        foreach ($album->tracks as $track) {
            $track->index = $index;
            $index++;
        }
        
        return $this->put('albums/' . $id, $album, 10080);
    }

    public function singles()
    {
        $singles = $this->get('singles/latest');
        if (!is_null($singles)) return $singles;

        $singles = Single::has('track')
                    ->with('track')
                    ->latest()
                    ->take(50)
                    ->get();
        
        return $this->put('singles/latest', $singles, 70);
    }

    public function tracks($id = '')
    {
        if (!empty($id)) {
            return $this->cacheTrack($id);
        } else {
            return $this->cacheTrendingTracks();
        }
    }

    public function cacheTrack($id)
    {
        $track = $this->get('tracks/' . $id);
        if (!is_null($track)) return $track;

        $track = Track::where('id', $id)->with('links')->firstOrFail();
        $track->index = 0;

        return $this->put('tracks/' . $id, $track, 10080);
    }

    public function cacheTrendingTracks()
    {
        $tracks = $this->get('tracks/trending');
        if (!is_null($tracks)) return $tracks;

        $tracks = Track::with('artists', 'downloads')
                    ->latest()
                    ->get();

        $trendingTracks = $tracks->sortByDesc(function ($track, $key) {
            $date = Carbon::parse('7 days ago');
            return $track->downloads()->whereDate('created_at', '>=', "{$date->year}-{$date->month}-{$date->day}")->count();
        })->take(10);

        return $this->put('tracks/trending', $trendingTracks, 70);
    }

    public function categories()
    {
        $categories = $this->get('categories/all');
        if (!is_null($categories)) return $categories;

        $categories = Category::with('genres')->get();

        return $this->put('categories/all', $categories, 10080);
    }

    public function genres()
    {
        $genres = $this->get('genres/all');
        if (!is_null($genres)) return $genres;

        $genres = Genre::get()->map(function ($genre) {
                $albums = Album::byGenre($genre);
                $singles = Single::byGenre($genre);
                if ($albums->exists() || $singles->exists()) {
                    return $genre;
                }
                return null;
            })->reject(null);

        return $this->put('genres/all', $genres, 1440);
    }

    public function settings($key = '')
    {
        if (!empty($key)) {
            return $this->cacheSetting($key);
        } else {
            Setting::each(function ($setting) {
                $this->cacheSetting($setting->key);
            });
            return;
        }
    }

    public function cacheSetting($key)
    {
        $setting = $this->get('settings/' . $key);
        if (!is_null($setting)) return $setting;

        $setting = Setting::where('key', $key)->first();

        return $this->put('settings/' . $key, $setting, 10080);
    }

    public function all()
    {
        $this->clear('albums/latest');
        $this->albums();

        $this->clear('singles/latest');
        $this->singles();

        $this->clear('tracks/trending');
        $this->tracks();

        $this->settings();
    }

    public function refresh()
    {
        $this->all();
    }

    private function put($key, $value, $time)
    {
        $minutes = Carbon::now()->addMinutes($time);
        Cache::put($key, $value, $minutes);

        return $this->get($key);
    }

    public function get($key)
    {
        return Cache::get($key);
    }

    public function clear($key = null)
    {
        if (!is_null($key)) {
            Cache::forget($key);
        }
        Cache::flush();
    }
}