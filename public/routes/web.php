<?php

use libs\Route;
use App\Controllers\BaseController;
use App\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index']);

Route::dispatch(str_replace('public/', '', $_SERVER['REQUEST_URI']), $_SERVER['REQUEST_METHOD']);



