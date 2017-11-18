<?php

namespace App\Models\Music\Album;

trait AlbumRelationship
{
	public function links()
	{
		return $this->morphMany(config('music.link.model'), 'linkable');
	}
	
	public function category()
	{
		return $this->belongsTo(config('music.category.model'));
	}

	public function genre()
	{
		return $this->belongsTo(config('music.genre.model'));
	}

	public function artists()
	{
		return $this->belongsToMany(config('music.artist.model'));
	}

	public function tracks()
	{
		return $this->morphMany(config('music.track.model'), 'trackable');
	}
}