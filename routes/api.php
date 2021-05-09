<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'throttle:6000,1'], function(){
	Route::post('login', [UserController::class, 'login']);
	Route::post('register', [UserController::class, 'register']);
});
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get("user/profile", [UserController::class, 'getProfile']);
	Route::get("user/upgrade-position", [UserController::class, 'upgradePosition']);
	Route::post("user/remove", [UserController::class, 'removeUser']);
});