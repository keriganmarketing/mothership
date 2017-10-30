<?php


Route::get('/', function () {
    return view('welcome');
});
Route::get('/api/v1/search', 'SearchController@index');
Route::get('/api/v1/listing/{mlsNumber}', 'ListingController@show');
Route::get('/api/v1/omnibar', 'OmnibarController@create');
Route::get('/api/v1/allMapListings', 'MapSearchController@index');
Route::get('/api/v1/agentlistings', 'AgentListingsController@index');
Route::get('/api/v1/agents', 'AgentSearchController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
