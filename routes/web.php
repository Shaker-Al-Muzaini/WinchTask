<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/test', 'test')->name('test');
