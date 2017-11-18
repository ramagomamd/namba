<?php

namespace App\Models\Music\Link;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
	protected $fillable = ['site_name', 'url'];
	
    public function linkable()
	{
		return $this->morphTo();
	}
}
