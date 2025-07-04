<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MessageController;

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


// Providers api
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/providers', [MessageController::class, 'getProviders']);
    Route::post('/messages', [MessageController::class, 'sendMessage']);
    // Route::get('/messages', [MessageController::class, 'getMessages']);
    Route::patch('/messages/{id}/status', [MessageController::class, 'updateStatus']);
});
Route::middleware('auth:sanctum')->get('/messages/{providerId}', [MessageController::class, 'getAllMessages']);
