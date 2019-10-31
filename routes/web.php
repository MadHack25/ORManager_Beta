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

Route::get('/','HomeController@index');

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();


Route::prefix('api')->group(function () {

    Route::get('/track/data','TrackerController@getTrackData');

    //Route::get('/track/only','TrackerController@trackOnly');
    
    Route::post('/track/{track}/on/{state}','TrackerController@trackWithPrice');

});


Route::prefix('packages')->group(function () {

    Route::get('/', 'PackageController@getPackages')->name('show-all');

    Route::post('/add/', 'PackageController@addNew')->name('addNew');

    Route::get('/sortby/{column}', 'PackageController@SortColumn')->name('sort-col');
    
    Route::post('/takeout/{tracking}/update/{value}', 'PackageController@updateTakeout')->name('updateTakeout');
    
    Route::post('/state/update/{id}', 'PackageController@updateState')->name('updateState');
    
    Route::post('/remove/{id}', 'PackageController@removeItem')->name('remove');

});