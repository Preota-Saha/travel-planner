<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    // columns we are allowed to mass-assign
    protected $fillable = ['location_id', 'place_id', 'distance_km'];

    // one route belongs to one starting location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // one route belongs to one destination place
    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
