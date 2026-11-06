<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    // columns we are allowed to mass-assign
    protected $fillable = ['category_id', 'name', 'slug', 'description', 'cover_image'];

    // one place belongs to one category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // one place has many experiences (attractions, food, shopping)
    public function experiences()
    {
        return $this->hasMany(PlaceExperience::class);
    }
}
