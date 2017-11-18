<?php

namespace App\Models\Music\Track;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Laravel\Scout\Searchable;
use Kyslik\ColumnSortable\Sortable;

class Track extends Model implements HasMediaConversions
{
    use TrackAttribute,
    	TrackRelationship,
    	TrackScope,
        HasMediaTrait,
        Sortable;

    protected $fillable = [
    			'title', 'slug', 'year', 'number', 
    			'comment', 'album', 'composer', 
    			'bitrate', 'duration', 'copyright'
    ];

    protected $sortable = [
                'id', 'title', 'slug', 'year', 'number', 
                'comment', 'album', 'composer', 
                'bitrate', 'duration', 'copyright',
                'created_at', 'updated_at'
    ];

    protected $with = ['artists', 'trackable.category', 'trackable.genre', 'media'];

    protected $appends = ['url', 'cover', 'full_title', 'artists_title_comma'];

    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
                ->performOnCollections('cover')
                ->width(100)
                ->height(100)
                ->sharpen(10)
                ->optimize();
    }

    public function toSearchableArray()
    {
        try {
            if (!is_null($this->trackable)) {
                if (!is_null($this->cover)) {
                    $cover = $this->cover->getFullUrl();
                } else {
                    $cover = null;
                }
                $track = $this->toArray();
                if ($this->trackable_type == 'albums') {
                    $track['album'] = $this->trackable->full_title;
                } else {
                    $track['album'] = "{$this->trackable->category->name} {$this->trackable->genre->name} Singles"; 
                }
                $track['downloads'] = $this->downloads->count();
                $track['cover'] = $cover;
                $track['route'] = $this->frontend_show_route;
                // dd($track);
                $track = array_only($track, [
                    'full_title',
                    'cover',
                    'album',
                    'route',
                    'downloads'
                ]);
               
                return $track;
            }

        } catch (\Exception $e) {
            return "Shit Happens";
        }
    }

    public function addMain(Model $artist)
    {
        $this->artists()->save($artist, ['role' => 'main']);
    }

    public function addFeature(Model $artist)
    {
        $this->artists()->save($artist, ['role' => 'feature']);
    }

    public function addProducer(Model $artist)
    {
        $this->artists()->save($artist, ['role' => 'producer']);
    }
}
