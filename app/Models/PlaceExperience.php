<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceExperience extends Model
{
    // columns we are allowed to mass-assign
    protected $fillable = ['place_id', 'type', 'title', 'description', 'image'];

    // one experience belongs to one place
    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
