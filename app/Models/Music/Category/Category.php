<?php

namespace App\Models\Music\Category;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use CategoryAttribute,
        CategoryRelationship,
    	CategoryScope,
        Sortable;

    protected $fillable = ['name', 'slug', 'description'];
    protected $sortable = ['id', 'name', 'slug', 'description'];

    protected $dates = ['deleted_at'];

    public function addGenre($genre)
    {
    	return $this->genres()->attach($genre);
    }

    public function removeGenre($genre)
    {
    	return $this->genres()->detach($genre);
    }
}
