<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserdataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// NO AUTH ROUTES

// REGISTER
Route::post('/register',[RegisterController::class,"store"]);

// USERS
Route::get('/users', [UserdataController::class,"index"]);

Route::get('/users/{id}', [UserdataController::class,"show"]);

Route::post('/users',[UserdataController::class,"store"]);

Route::put('/users',[UserdataController::class,"update"]);

Route::delete('/users', [UserdataController::class,"destroy"]);

// AUTH ROUTES GROUP
Route::group(['middleware' => 'auth:api'],function() {
    Route::post('/testOauth', [RegisterController::class,"testOauth"]);
});


