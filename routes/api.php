<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\HomeController;

Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthApiController::class, 'user']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
});


Route::apiResource('/home',HomeController::class)->middleware('auth:sanctum');

// Booking api

Route::post('/booking',[BookingController::class,'store'])->middleware('auth:sanctum');
Route::get('/booking',[BookingController::class,'index'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/booking/cancel', [BookingController::class, 'cancel']);

