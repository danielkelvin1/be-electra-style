<?php

use App\Http\Controllers\user\AuthController;
use App\Http\Controllers\user\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

route::group(['middleware' => 'auth:api', 'controller' => UserController::class], function () {
    Route::get('/user', 'getUser');
    Route::post('/user/picture', 'uploadPicutre');
    Route::put('/user', 'updateProfile');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login',  'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth:api');
});
