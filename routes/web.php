<?php


Route::get('/', function () {
    return view('welcome');
});
Route::prefix('api/v1')->group(function () {
    Route::get('search', 'ListingsSearchController@index');
    Route::get('listing/{mlsNumber}', 'ListingController@show');
    Route::get('listings', 'ListingController@index');
    Route::get('omnibar', 'OmnibarController@create');
    Route::get('allMapListings', 'MapSearchController@index');
    Route::get('agentlistings', 'AgentListingsController@index');
    Route::get('agents', 'AgentSearchController@index');
    Route::get('updatedListings', 'UpdatedListingsController@index');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
