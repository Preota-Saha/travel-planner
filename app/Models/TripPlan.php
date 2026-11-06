<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripPlan extends Model
{
    // columns we are allowed to mass-assign
    protected $fillable = [
        'location_id',
        'place_id',
        'transport_id',
        'accommodation_id',
        'people',
        'days',
        'transport_cost',
        'accommodation_cost',
        'total_cost',
    ];

    // one trip plan belongs to one starting location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // one trip plan belongs to one destination place
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    // one trip plan uses one transport mode
    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    // one trip plan uses one accommodation option
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }
}
