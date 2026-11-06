<?php

use App\Http\Controllers\Api\AccommodationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\TransportController;
use App\Http\Controllers\Api\TripPlanController;
use Illuminate\Support\Facades\Route;

// list all destination categories
Route::get('/categories', [CategoryController::class, 'index']);

// list all places under one category by its slug
Route::get('/categories/{slug}/places', [CategoryController::class, 'places']);

// show full details for one place by its slug
Route::get('/places/{slug}', [PlaceController::class, 'show']);

// list all starting locations for the trip planner
Route::get('/locations', [LocationController::class, 'index']);

// list all transport options for the trip planner
Route::get('/transports', [TransportController::class, 'index']);

// list all accommodation options for the trip planner
Route::get('/accommodations', [AccommodationController::class, 'index']);

// calculate the estimated cost for a trip without saving it
Route::post('/trip-plan/calculate', [TripPlanController::class, 'calculate']);

// save a new trip plan to the database and return it with its new id
Route::post('/trip-plans', [TripPlanController::class, 'store']);

// fetch one saved trip plan by its id with related names
Route::get('/trip-plans/{id}', [TripPlanController::class, 'show'])->whereNumber('id');
