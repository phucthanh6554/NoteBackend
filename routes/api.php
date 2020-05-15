<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// header('Access-Control-Allow-Origin', '*');
// header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization');

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/user/create', 'Users@create');
Route::post('/user/login', 'Users@login');

Route::middleware(['jwt_verified'])->group(function(){
    Route::post('/user/changePassword', 'Users@changePassword');

    // Notebook route
    Route::get('/notebook/show/{user_id}', 'Notebooks@showAll')->where('user_id', '[0-9]+');
    Route::post('/notebook/create', 'Notebooks@create');
    Route::post('/notebook/update', 'Notebooks@update');
    Route::post('/notebook/delete', 'Notebooks@delete');
});