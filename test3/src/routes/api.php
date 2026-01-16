<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\VideoPostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    });

    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/{id}', [NewsController::class, 'show'])->name('show');
        Route::post('/', [NewsController::class, 'store'])->name('store');
    });

    Route::prefix('video-posts')->name('video-posts.')->group(function () {
        Route::get('/', [VideoPostController::class, 'index'])->name('index');
        Route::get('/{id}', [VideoPostController::class, 'show'])->name('show');
        Route::post('/', [VideoPostController::class, 'store'])->name('store');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::get('user', [AuthController::class, 'user'])->name('auth.user');
        });

        Route::apiResource('comments', CommentController::class)->except(['index']);

        Route::prefix('comments/{comment}')->name('comments.')->group(function () {
            Route::get('replies', [CommentController::class, 'replies'])->name('replies');
        });
    });
});
