<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserdataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// NO AUTH ROUTES
Route::post('/register',[RegisterController::class,"store"]);

Route::get('/users', [UserdataController::class,"getUsers"]);

// AUTH ROUTES GROUP
Route::group(['middleware' => 'auth:api'],function() {
    Route::post('/testOauth', [RegisterController::class,"testOauth"]);
});


