<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\AuthApiController;

Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthApiController::class, 'user']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
});


Route::apiResource('events', EventsController::class);
