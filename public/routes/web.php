<?php

use libs\Route;
use App\Controllers\BaseController;
use App\Controllers\HomeController;
use App\Controllers\AuthController;


Route::get('/', [HomeController::class, 'index']);
Route::get('/login', [AuthController::class, 'login']);
Route::get('/callback/:code/:status', [AuthController::class, 'callback']);

Route::dispatch(str_replace('public/', '', $_SERVER['REQUEST_URI']), $_SERVER['REQUEST_METHOD']);



