<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    // columns we are allowed to mass-assign
    protected $fillable = ['name', 'star_rating', 'price_per_night'];
}
