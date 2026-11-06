<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    // return all starting locations for the trip planner dropdown
    public function index(): JsonResponse
    {
        $locations = Location::all(['id', 'name']);

        return response()->json($locations);
    }
}
