<?php

use App\Http\Controllers\product\ProductController;
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
    Route::prefix('user')->group(function () {
        Route::get('/', 'getUser');
        Route::post('/picture', 'uploadPicutre');
        Route::post('/address', 'addAddress');
        Route::put('/address/{id}', 'updateAddress');
        Route::delete('/address/{id}', 'deleteAddress');
        Route::put('/', 'updateProfile');
    });
});

Route::group(['middleware' => 'auth:api', 'controller' => ProductController::class], function () {
    Route::prefix('product')->group(function () {
        Route::post('/', 'addProduct');
        Route::put('/{id}', 'editProduct');
        Route::delete('/image/{id}', 'deleteImageProduct');
        Route::post('/image', 'addImageProduct');
        Route::delete('/{id}', 'deleteProduct');
        Route::get('/', 'getAllProduct');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login',  'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth:api');
});
