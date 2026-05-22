<?php

use Illuminate\Support\Facades\Route;
use Src\Presentation\Admin\Controllers\OrderAssignmentController;

// مسارات لوحة تحكم الإدارة
Route::prefix('admin')->group(function () {
    Route::get('/orders', [OrderAssignmentController::class, 'index']);
    Route::post('/orders/{id}/assign', [OrderAssignmentController::class, 'assign']);
});
