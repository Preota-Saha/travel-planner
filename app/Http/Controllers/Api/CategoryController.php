<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    // return all categories for the home screen
    public function index(): JsonResponse
    {
        $categories = Category::all(['id', 'name', 'slug', 'image']);

        return response()->json($categories);
    }

    // return all places that belong to one category by its slug
    public function places(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $places = $category->places()->get(['id', 'name', 'slug', 'description', 'cover_image']);

        return response()->json($places);
    }
}
