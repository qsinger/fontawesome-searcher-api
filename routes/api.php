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

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact the technical support'
    ], 404);
});

Route::prefix('5.12.0')->group(function() {

    Route::apiResource('categories', 'API\CategoryController')->only([
        'index', 'show'
    ]);

    Route::get('/categories/{categorieId}/icons', 'API\IconController@getByCategory');

    Route::apiResource('icons', 'API\IconController')->only([
        'index', 'show'
    ]);

//    Route::apiResources([
//        'categories' => 'API\CategoryController',
//        'icons' => 'API\IconController',
//        'search-terms' => 'API\SearchTermController'
//    ]);

});
