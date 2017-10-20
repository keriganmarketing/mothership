<?php

Route::get('/', function () {
    return view('welcome');
});
Route::get('/api/v1/search', 'SearchController@index');
Route::get('/api/v1/listing/{mlsNumber}', 'ListingController@show');
Route::get('/api/v1/omnibar', 'OmnibarController@create');
