<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\JsonResponse;

class PlaceController extends Controller
{
    // get one place and all its experiences by slug
    public function show(string $slug): JsonResponse
    {
        $place = Place::where('slug', $slug)
            ->with('experiences')
            ->first();

        if (!$place) {
            return response()->json(['message' => 'Place not found'], 404);
        }

        // group the experiences by their type for the frontend
        $grouped = $place->experiences->groupBy('type')->map(function ($items) {
            return $items->map(function ($i) {
                return [
                    'id' => $i->id,
                    'title' => $i->title,
                    'description' => $i->description,
                    'image' => $i->image,
                ];
            })->values();
        });

        return response()->json([
            'id' => $place->id,
            'category_id' => $place->category_id,
            'name' => $place->name,
            'slug' => $place->slug,
            'description' => $place->description,
            'cover_image' => $place->cover_image,
            'experiences' => [
                'attractions' => $grouped->get('attraction', collect()),
                'foods' => $grouped->get('food', collect()),
                'shopping' => $grouped->get('shopping', collect()),
            ],
        ]);
    }
}
