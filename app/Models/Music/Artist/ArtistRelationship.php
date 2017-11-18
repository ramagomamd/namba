<?php

namespace App\Models\Music\Artist;

trait ArtistRelationship
{
	public function tracks()
	{
		return $this->belongsToMany(config('music.track.model'))
				->withPivot('role');
	}

	public function getTracks($type)
	{
        switch($type) {
            case 'main': 
                return $this->tracks()->wherePivot('type','main');
            case 'feature': //returns films with this person in cast
                return $this->tracks()->wherePivot('type', 'feature');
            case 'producer': //returns films with this person in cast
                return $this->tracks()->wherePivot('type', 'producer');
            default:
                return $this->tracks;
        }
    }

	public function albums()
	{
		return $this->belongsToMany(config('music.album.model'));
	}

	public function singles()
	{
		return $this->belongsToMany(config('music.single.model'));
	}
}