<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Route as TripRoute;
use App\Models\Transport;
use App\Models\TripPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripPlanController extends Controller
{
    // calculate the estimated trip cost without saving anything to the database
    public function calculate(Request $request): JsonResponse
    {
        $cost = $this->buildCost($request);

        // if no route was found, return a 404 error
        if (isset($cost['error'])) {
            return response()->json(['message' => $cost['error']], 404);
        }

        return response()->json($cost);
    }

    // validate input, calculate the cost and save the trip plan to the database
    public function store(Request $request): JsonResponse
    {
        $cost = $this->buildCost($request);

        // if no route was found, return a 404 error
        if (isset($cost['error'])) {
            return response()->json(['message' => $cost['error']], 404);
        }

        // save the calculated plan as a new trip_plans row
        $plan = TripPlan::create([
            'location_id' => $cost['location_id'],
            'place_id' => $cost['place_id'],
            'transport_id' => $cost['transport_id'],
            'accommodation_id' => $cost['accommodation_id'],
            'people' => $cost['people'],
            'days' => $cost['days'],
            'transport_cost' => $cost['transport_cost'],
            'accommodation_cost' => $cost['accommodation_cost'],
            'total_cost' => $cost['total_cost'],
        ]);

        // reload with related names so the response has them
        $plan->load(['location', 'place', 'transport', 'accommodation']);

        return response()->json($this->formatPlan($plan), 201);
    }

    // return one saved trip plan with its related names
    public function show(int $id): JsonResponse
    {
        $plan = TripPlan::with(['location', 'place', 'transport', 'accommodation'])->find($id);

        if (!$plan) {
            return response()->json(['message' => 'Trip plan not found'], 404);
        }

        return response()->json($this->formatPlan($plan));
    }

    // shared helper: validate the request, look up the route, transport and accommodation, then compute the cost
    private function buildCost(Request $request): array
    {
        // check the request body has all required fields with valid values
        $data = $request->validate([
            'location_id' => 'required|integer',
            'place_id' => 'required|integer',
            'transport_id' => 'required|integer',
            'accommodation_id' => 'required|integer',
            'people' => 'required|integer|min:1',
            'days' => 'required|integer|min:1',
        ]);

        // find the distance between the chosen location and the chosen place
        $route = TripRoute::where('location_id', $data['location_id'])
            ->where('place_id', $data['place_id'])
            ->first();

        if (!$route) {
            return ['error' => 'No route found for this location and place'];
        }

        // look up the chosen transport's price per km
        $transport = Transport::find($data['transport_id']);
        // look up the chosen accommodation's price per night
        $accommodation = Accommodation::find($data['accommodation_id']);

        $distanceKm = (float) $route->distance_km;
        $pricePerKm = (float) $transport->price_per_km;
        $pricePerNight = (float) $accommodation->price_per_night;
        $people = (int) $data['people'];
        $days = (int) $data['days'];

        // round trip cost = distance x price x people x 2
        $transportCost = $distanceKm * $pricePerKm * $people * 2;

        // room cost = price per night x days x rooms (2 people per room, round up)
        $rooms = (int) ceil($people / 2);
        $accommodationCost = $pricePerNight * $days * $rooms;

        // total estimated cost for the whole trip
        $totalCost = $transportCost + $accommodationCost;

        // return all values, rounded for clean JSON, plus the ids needed to save the plan
        return [
            'location_id' => $data['location_id'],
            'place_id' => $data['place_id'],
            'transport_id' => $data['transport_id'],
            'accommodation_id' => $data['accommodation_id'],
            'people' => $people,
            'days' => $days,
            'distance_km' => round($distanceKm, 2),
            'transport_cost' => round($transportCost, 2),
            'accommodation_cost' => round($accommodationCost, 2),
            'total_cost' => round($totalCost, 2),
        ];
    }

    // shared helper: turn a TripPlan model into a JSON-friendly array with the related names
    private function formatPlan(TripPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'location_id' => $plan->location_id,
            'place_id' => $plan->place_id,
            'transport_id' => $plan->transport_id,
            'accommodation_id' => $plan->accommodation_id,
            'people' => $plan->people,
            'days' => $plan->days,
            'transport_cost' => (float) $plan->transport_cost,
            'accommodation_cost' => (float) $plan->accommodation_cost,
            'total_cost' => (float) $plan->total_cost,
            'created_at' => $plan->created_at,
            'updated_at' => $plan->updated_at,
            'location' => $plan->location ? ['id' => $plan->location->id, 'name' => $plan->location->name] : null,
            'place' => $plan->place ? ['id' => $plan->place->id, 'name' => $plan->place->name, 'slug' => $plan->place->slug] : null,
            'transport' => $plan->transport ? ['id' => $plan->transport->id, 'name' => $plan->transport->name] : null,
            'accommodation' => $plan->accommodation ? ['id' => $plan->accommodation->id, 'name' => $plan->accommodation->name] : null,
        ];
    }
}
