<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ImageGenerationController;
use App\Http\Controllers\Api\V1\UserManagementController;
use App\Http\Controllers\RolePermissionController;

use Laravel\Socialite\Socialite;
Route::middleware('auth:sanctum')->group(function () {
   Route::get('/user', function (Request $request) {

    $user = $request->user();
    return response()->json([
        'user' => $user,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name')
    ]);
   });

   Route::apiResource('/users', UserManagementController::class)->only(['index'])->middleware('permission:users.view');
   Route::apiResource('/users', UserManagementController::class)->only(['edit','update'])->middleware('permission:users.edit');

   Route::get('/permissions/sync-roles', [RolePermissionController::class, 'syncRoles']);
   Route::apiResource('/permissions', RolePermissionController::class);
   Route::get('/permissions/user/{id}', [RolePermissionController::class, 'userPermissions']);
   Route::get('/roles/list', [RolePermissionController::class, 'roleList']);

   Route::prefix('v1')->group(function () {
    Route::apiResource('posts', PostController::class);
    Route::apiResource('image-generation', ImageGenerationController::class)->only(['index', 'store']);
   });
});



require __DIR__.'/auth.php';
