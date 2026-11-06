<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    // columns we are allowed to mass-assign
    protected $fillable = ['name', 'price_per_km', 'icon'];
}
