<?php

use Illuminate\Support\Facades\Route;
use Src\Presentation\Admin\Controllers\OrderAssignmentController; // نفس المسار تماماً
Route::get('/index', [OrderAssignmentController::class, 'renderDashboard']);
