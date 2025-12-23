<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
})->middleware('throttle:10,1');
