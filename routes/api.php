<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes (Unauthenticated)
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register')->name('api.register');
    Route::post('/login', 'login')->name('api.login');
    Route::get('/email/verify/{id}/{hash}', 'verify')
        ->middleware(['signed'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', 'resend')
        ->middleware(['auth:sanctum', 'throttle:6,1'])
        ->name('verification.send');
});

// Protected Routes (Authenticated with Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // Authentication Routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout')->name('api.logout');
        Route::get('/user', 'user')->name('api.user');
    });

    // Posts Resource Routes
    Route::apiResource('posts', PostController::class);

    // Like Routes
    Route::controller(LikeController::class)->group(function () {
        Route::post('/posts/{post}/like', 'like')->name('api.posts.like');
        Route::delete('/posts/{post}/unlike', 'unlike')->name('api.posts.unlike');
    });

    // Comment Routes
    Route::controller(CommentController::class)->group(function () {
        Route::get('/posts/{post}/comments', 'index')->name('api.posts.comments.index');
        Route::post('/posts/{post}/comments', 'store')->name('api.posts.comments.store');
        Route::put('/comments/{comment}', 'update')->name('api.comments.update');
        Route::patch('/comments/{comment}', 'update')->name('api.comments.patch');
        Route::delete('/comments/{comment}', 'destroy')->name('api.comments.destroy');
    });

    // Activity Logs Routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('api.activity-logs.index');
});
