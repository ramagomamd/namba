<?php

namespace App\Models\Music\Track;

trait TrackRelationship
{
	public function trackable()
	{
		return $this->morphTo();
	}

	public function downloads()
	{
		return $this->morphMany(config('music.download.model'), 'downloadable');
	}

    public function links()
	{
		return $this->morphMany(config('music.link.model'), 'linkable');
	}

	public function artists()
	{
		return $this->belongsToMany(config('music.artist.model'))
					->withPivot('role');
	}

	public function main()
	{
		return $this->belongsToMany(config('music.track.model'))
					->wherePivot('role', 'main');
	}

	public function features()
	{
		return $this->belongsToMany(config('music.track.model'))
					->wherePivot('role', 'feature');
	}

	public function producer()
	{
		return $this->belongsToMany(config('music.track.model'))
					->wherePivot('role', 'producer');
	}
}