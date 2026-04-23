<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ImageGenerationController;
use App\Http\Controllers\Api\V1\UserManagementController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\CartController;
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

   Route::apiResource('/users', UserManagementController::class);

   Route::get('/permissions/sync-roles', [RolePermissionController::class, 'syncRoles']);
   Route::apiResource('/permissions', RolePermissionController::class);
   Route::get('/permissions/user/{id}', [RolePermissionController::class, 'userPermissions']);
   Route::get('/roles/list', [RolePermissionController::class, 'roleList']);

   Route::prefix('v1')->group(function () {
    Route::apiResource('/categories', CategoryController::class);
    Route::post('/categories/update/{category_id}', [CategoryController::class, 'update_category']);
    Route::get('/categories/items/{category_id}', [CategoryController::class, 'category_items']);
    Route::apiResource('/items', ItemController::class);
    Route::post('/items/update/{item_id}', [ItemController::class, 'update_item']);
    Route::apiResource('/posts', PostController::class);
    Route::apiResource('/carts', CartController::class);
    Route::apiResource('/image-generation', ImageGenerationController::class)->only(['index', 'store']);
   });
});



require __DIR__.'/auth.php';
