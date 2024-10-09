<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('register', 'register');
});
Route::prefix('admin')->middleware(['auth:api', 'is_admin'])->group(function () {
    Route::apiResource('books', BookController::class);
    Route::get('/deletedBooks', [BookController::class, 'deletedBooks']);
    Route::post('/books/{book}/restore', [BookController::class, 'restoreBook']);
    Route::delete('/books/{book}/finalDelete', [BookController::class, 'forceDeleteBook']);

    Route::apiResource('categories', CategoryController::class);
    Route::get('/deletedCategory', [CategoryController::class, 'deletedCategory']);
    Route::post('/categories/{category}/restore', [CategoryController::class, 'restoreCategory']);
    Route::delete('/categories/{category}/finalDelete', [CategoryController::class, 'forceDeleteCategory']);

    Route::apiResource('roles', RoleController::class);
    Route::get('/deletedRoles', [RoleController::class, 'deletedRoles']);
    Route::post('/roles/{role}/restore', [RoleController::class, 'restoreRole']);
    Route::delete('/roles/{role}/finalDelete', [RoleController::class, 'forceDeleteRole']);
    Route::post('/roles/{role}/addPermissions', [RoleController::class, 'addPermissionToRole']);
    Route::post('/roles/{role}/removePermission', [RoleController::class, 'removePermissionToRole']);

    Route::apiResource('permissions', PermissionController::class);
    Route::get('/deletedPermissions', [PermissionController::class, 'deletedPermissions']);
    Route::post('/permissions/{permission}/restore', [PermissionController::class, 'restorePermission']);
    Route::delete('/permissions/{permission}/finalDelete', [PermissionController::class, 'forceDeletePermission']);

    Route::apiResource('users', UserController::class);
    Route::get('/deletedUsers', [UserController::class, 'allDeletedUsers']);
    Route::post('/users/{user}/restore', [UserController::class, 'restoreUser']);
    Route::delete('/users/{user}/finalDelete', [UserController::class, 'forceDeleteUser']);

    Route::apiResource('orders', OrderController::class)->except(['store', 'update', 'delete']);
    Route::post('/orders/{order}/accept', [OrderController::class, 'accept']);
    Route::post('/orders/{order}/reject', [OrderController::class, 'reject']);
});

Route::prefix('salesManager')->middleware(['auth:api', 'is_sales_manager'])->group(function () {

    Route::apiResource('users', UserController::class)->only(['index', 'show']);
    Route::apiResource('orders', OrderController::class)->except(['store', 'update', 'delete']);
    Route::post('/orders/{order}/accept', [OrderController::class, 'accept']);
    Route::post('/orders/{order}/reject', [OrderController::class, 'reject']);
});

Route::prefix('customer')->middleware(['auth:api', 'is_customer'])->group(function () {
    Route::apiResource('books', BookController::class)->only(['index', 'show']);
    Route::apiResource('carts', CartController::class)->except('show');
    Route::apiResource('orders', OrderController::class)->except(['store', 'update']);
    Route::post('/confirmOrder', [OrderController::class, 'confirm']);
});

Route::prefix('delivery')->middleware(['auth:api', 'is_delivery'])->group(function () {
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
    Route::post('/orders/{order}/start', [OrderController::class, 'start']);
    Route::post('/orders/{order}/end', [OrderController::class, 'end']);
});