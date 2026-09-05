<?php

use App\Http\Controllers\GearController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/users', UserController::class);

Route::apiResource("/gears", GearController::class);

Route::apiResource("/rentals", RentalController::class);