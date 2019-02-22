<?php


Route::prefix('permiso')->group(function () {

    Route::namespace('Betalectic\Permiso\Http\Controllers')->group(function () {
        Route::get('groups', 'GroupController@index');
    });
});


