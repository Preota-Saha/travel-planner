<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use Illuminate\Http\JsonResponse;

class AccommodationController extends Controller
{
    // return all accommodation options for the trip planner dropdown
    public function index(): JsonResponse
    {
        $accommodations = Accommodation::all(['id', 'name', 'star_rating', 'price_per_night']);

        return response()->json($accommodations);
    }
}
