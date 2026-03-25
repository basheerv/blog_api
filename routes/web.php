<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
Route::get('/test', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);


require __DIR__.'/auth.php';
