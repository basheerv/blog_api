<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ImageGenerationController;


Route::middleware('auth:sanctum')->group(function () {
   Route::get('/user', function (Request $request) {
    return $request->user();
   });

   Route::prefix('v1')->group(function () {
    Route::apiResource('posts', PostController::class);
    Route::apiResource('image-generation', ImageGenerationController::class)->only(['index', 'store']);
   });
});


require __DIR__.'/auth.php';
