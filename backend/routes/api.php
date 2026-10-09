<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GearController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/users', UserController::class);

Route::middleware('auth:sanctum')->group(function () {
    // Gears
    Route::post("/gears", [GearController::class, "store"]);
    Route::put("/gears/{gear}", [GearController::class, "update"]);
    Route::delete("/gears/{gear}", [GearController::class, "destroy"]);
    Route::get('/mygears', [GearController::class, "mine"]);
    Route::patch('/gears/{gear}/status', [GearController::class, "updateStatus"]);

    // Rentals
    Route::post('/rentals', [RentalController::class, 'store']);
    Route::get('/myrentals', [RentalController::class, 'mine']);
    Route::patch('/rentals/{rental}', [RentalController::class, 'update']);
    Route::get('/rentals/incoming', [RentalController::class, 'incoming']);
    Route::get('/rentals/{rental}', [RentalController::class, 'show']);

    // Ratings
    Route::post('/rentals/{rental}/ratings', [RatingController::class, 'store']);
    Route::get('/myratings', [RatingController::class, 'received']);

    // Handover
    Route::post('/rentals/{rental}/confirm-handover', [RentalController::class, 'confirmHandover']);
});


Route::get("/gears", [GearController::class, "index"]);
Route::get("/gears/{gear}", [GearController::class, "show"]);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
