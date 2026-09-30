<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GearController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\UserController;
use App\Models\Gear;
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
    Route::patch('/rentals/{rental}', [RentalController::class, 'update']);
});


Route::get("/gears", [GearController::class, "index"]);
Route::get("/gears/{gear}", [GearController::class, "show"]);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
