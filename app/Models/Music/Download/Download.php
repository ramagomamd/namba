<?php

namespace App\Models\Music\Download;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    public function downloadable()
	{
		return $this->morphTo();
	}
}
