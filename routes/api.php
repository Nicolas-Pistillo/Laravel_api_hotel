<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserdataController;
use App\Http\Controllers\ConfirmationsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;

// NO AUTH ROUTES

// REGISTER
Route::post('/register',[RegisterController::class,"store"]);

// USERS

Route::put('users', [UserdataController::class,"update"]);

Route::delete('users', [UserdataController::class,"destroy"]);

Route::resource('users', UserdataController::class);

// ACTIVITIES

Route::put('activities', [ActivityController::class,"update"]);

Route::put('activities/{id}',[ActivityController::class,"changeActive"]);

Route::resource('activities',ActivityController::class);

// CONFIRMATIONS

Route::delete('confirmations', [ConfirmationsController::class,"delete"]);

Route::get('userconfirms/{id}', [ConfirmationsController::class,"userConfirms"]);

Route::resource('confirmations', ConfirmationsController::class);


// ONE SIGNAL

Route::put('users/addOneSignal/{id}', [UserdataController::class,"addOneSignal"]);

// AUTH ROUTES GROUP
Route::group(['middleware' => 'auth:api'],function() {
    Route::post('/testOauth', [RegisterController::class,"testOauth"]);
});


