<?php

use Illuminate\Support\Facades\Route;
use Src\Presentation\Admin\Controllers\OrderAssignmentController;
use Src\Presentation\Api\Controllers\ApiDriverController;
use Src\Presentation\Api\Controllers\ApiOrderController;
use Src\Presentation\Api\Controllers\AssignOrderController;


// مسارات لوحة تحكم الإدارة
Route::prefix('admin')->group(function () {
    Route::get('/orders', [OrderAssignmentController::class, 'index']);
    Route::post('/orders/{id}/assign', [OrderAssignmentController::class, 'assign']);
});

Route::post('/drivers', [ApiDriverController::class, 'store']);
Route::post('/orders', [ApiOrderController::class, 'store']);

Route::post('/orders/{id}/assign', AssignOrderController::class);
