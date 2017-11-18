<?php

namespace App\Models\Music\Genre;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Genre extends Model
{
    use GenreAttribute,
    	GenreRelationship,
    	GenreScope,
    	Sortable;

    protected $fillable = ['name', 'slug', 'description'];

    protected $sortable = ['id', 'name', 'slug', 'description'];

    protected $dates = ['deleted_at'];
}
