<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use Illuminate\Http\JsonResponse;

class TransportController extends Controller
{
    // return all transport options for the trip planner dropdown
    public function index(): JsonResponse
    {
        $transports = Transport::all(['id', 'name', 'price_per_km', 'icon']);

        return response()->json($transports);
    }
}
