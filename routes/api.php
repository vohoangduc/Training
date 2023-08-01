<?php

use App\Http\Controllers\Admin\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('list-user', [UserController::class, 'listUser']);
        Route::post('create-user', [UserController::class, 'createUser']);
        Route::get('delete/{id}', [UserController::class, 'deleteUser']);
        Route::delete('delete/{id}', [UserController::class, 'deleteUser']);
    });
});
