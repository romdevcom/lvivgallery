<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/sitemap.xml', 'ObjectController@sitemap');

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
		Route::get('/', 'ObjectController@index');
//        Route::get('test', 'ObjectController@test');
//        Route::get('/about', 'HomeController@about');
//        Route::get('/user', 'UserController@index');
//        Route::get('/user/registration', 'UserController@registration');
//        Route::get('/user/restore', 'UserController@restore');
//        Route::get('/user/change', 'UserController@change');
//        Route::get('/user/edit', 'UserController@edit');
//        Route::get('/user/admin', 'UserController@admin');
//        Route::get('/user/login', 'UserController@login');
//        Route::get('/faq', 'HomeController@faq');
//        Route::get('photos', 'PhotoController@index');
//        Route::get('photos/{id}', 'PhotoController@show');
//        Route::get('videos', 'VideoController@index');
//        Route::get('videos/{id}', 'VideoController@show');
//        Route::get('collections', 'CollectionController@index');
//        Route::get('collections/{id}', 'CollectionController@show');
//        Route::get('collections/{id}/photos', 'CollectionController@photos');
//        Route::get('collections/{id}/videos', 'CollectionController@videos');
//        Route::get('collections/{id}/maps', 'CollectionController@maps');
//        Route::get('collections/{id}/interviews', 'CollectionController@interviews');
//		Route::get('messages', 'MessageController@index');
//		Route::get('messages/{id}', 'MessageController@show');
//		Route::get('interviews', 'InterviewController@index');
//		Route::get('interviews/{id}', 'InterviewController@show');
//        Route::get('maps', 'MapsController@index');
//        Route::get('maps/{id}', 'MapsController@show');
        Route::get('object/{slug}', 'ObjectController@show');

        Route::post('search-objects', 'ObjectController@search_object');
        Route::post('get-more-objects', 'ObjectController@get_more_objects');
    }
);