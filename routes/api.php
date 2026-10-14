<?php

use App\Http\Controllers\AdjustmentReasonController;
use App\Http\Controllers\InventoryAdjustmentController;
use Illuminate\Support\Facades\Route;

Route::get('/adjustment-reasons/list', [AdjustmentReasonController::class, 'list']);
Route::post('/inventory-adjustments/store', [InventoryAdjustmentController::class, 'store']);
Route::get('/inventory-adjustments/show/{inventoryAdjustment}', [InventoryAdjustmentController::class, 'show']);
