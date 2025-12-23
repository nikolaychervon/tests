<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:20,1')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::get('/list', [UserController::class, 'list']);
});
