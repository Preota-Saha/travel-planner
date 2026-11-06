<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // columns we are allowed to mass-assign
    protected $fillable = ['name', 'slug', 'image'];

    // one category has many places
    public function places()
    {
        return $this->hasMany(Place::class);
    }
}
