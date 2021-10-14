<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('App\Http\Controllers')->group(function () {

    // GET API
    Route::get('users/{id?}', 'APIController@getUsers');
    Route::get('users-list', 'APIController@getUsersList');
    Route::post('add-user', 'APIController@addUser');
    // Register User  with API Token
    Route::post('register-user', 'APIController@registerUser');

    // Login User Update Token with API
    Route::post('login-user', 'APIController@loginUser');

    // Login User Update Token with API
    Route::post('logout-user', 'APIController@logoutUser');

    Route::post('add-users', 'APIController@addUsers');
    Route::put('update-user/{id?}', 'APIController@updateUser');
    Route::patch('update-user-name/{id?}', 'APIController@updateUserName');
    Route::delete('delete-user/{id?}', 'APIController@deleteUser');
    Route::delete('delete-user', 'APIController@deleteUserJson');
    Route::delete('delete-multi-user/{ids}', 'APIController@deleteUserMulti');
});
