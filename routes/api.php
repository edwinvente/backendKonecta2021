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

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {
    /*AÑADE AQUI LAS RUTAS QUE QUIERAS PROTEGER CON JWT*/
    Route::get('refresh', 'UserController@refresh');
    Route::get('logout', 'UserController@logout');
    Route::get('me', 'UserController@me');
    Route::get('user/profile', 'UserController@profile');
    //routes categories
    Route::resource('categories', 'CategoryController');
    Route::resource('posts', 'PostController');
    Route::post('posts/comment', 'PostController@comment');
    Route::post('posts/comments/{slug}', 'PostController@comments');
});
Route::get('/post/file/{filename}', 'PostController@getImage');