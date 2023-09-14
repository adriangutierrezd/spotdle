<?php

use libs\Route;
use App\Controllers\BaseController;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\HintController;
use App\Controllers\GameController;
use App\Controllers\GamePathController;

use App\Middleware\AuthMiddleware;


Route::get('/', [HomeController::class, 'index']);
Route::get('/login', [AuthController::class, 'login']);
Route::get('/callback/:code/:status', [AuthController::class, 'callback']);
Route::get('/error-404', [BaseController::class, 'error404']);
Route::get('/error-500', [BaseController::class, 'error500']);

Route::get('/api/hints/:game_type', [HintController::class, 'getHints'], [AuthMiddleware::class]);

Route::get('/api/games/:gameId', [GameController::class, 'get']);
Route::post('/api/games', [GameController::class, 'create']);
Route::put('/api/games/:gameId', [GameController::class, 'update']);

Route::get('/game-path', [GamePathController::class, 'generate'], [AuthMiddleware::class]);

Route::dispatch(str_replace('public/', '', $_SERVER['REQUEST_URI']), $_SERVER['REQUEST_METHOD']);



