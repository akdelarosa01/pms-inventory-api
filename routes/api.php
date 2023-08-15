<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\InventoryHistoryController;

header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

Route::post('login', [LoginController::class, 'authenticate']);
Route::post('logout', [LoginController::class, 'logout']);

Route::get('/items-status', [ItemController::class, 'item_status']);
Route::apiResource('/items', ItemController::class);
Route::delete('/items-delete', [ItemController::class, 'destroy']);

Route::apiResource('/inventories', InventoryController::class);
Route::delete('/inventory-items-delete', [InventoryController::class, 'destroy']);
Route::get('/inventories-item-types', [InventoryController::class, 'get_item_types']);
Route::get('/inventories-warehouse', [InventoryController::class, 'get_warehouse']);
Route::get('/inventories-items', [InventoryController::class, 'get_items']);


Route::apiResource('/inventory-history', InventoryHistoryController::class);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
