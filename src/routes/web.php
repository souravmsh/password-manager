<?php

Route::prefix('password-manager')
->name('password-manager.')
->namespace('Souravmsh\PasswordManager\Http\Controllers')
->middleware(['web', 'auth'])
->group(function() {  
    Route::get('/', 'PasswordManagerController@index')->name('expiry');
    Route::get('/expiry/create', 'PasswordManagerController@create')->name('expiry.create');
    Route::post('/expiry', 'PasswordManagerController@store')->name('expiry.save');
    Route::get('/expiry/{id}', 'PasswordManagerController@edit')->name('expiry.edit');
    Route::put('/expiry/{id}', 'PasswordManagerController@update')->name('expiry.update');

    Route::get('/rules', 'RulesController@index')->name('rules');
    Route::get('/rules/create', 'RulesController@create')->name('rules.create');
    Route::put('/rules/update', 'RulesController@update')->name('rules.update');

    Route::get('/checklist', 'ChecklistController@index')->name('checklist');
    Route::get('/checklist/create', 'ChecklistController@create')->name('checklist.create');
    Route::post('/checklist', 'ChecklistController@store')->name('checklist.save');
    Route::get('/checklist/{id}', 'ChecklistController@edit')->name('checklist.edit');
    Route::put('/checklist/{id}', 'ChecklistController@update')->name('checklist.update');

    Route::get('/pretend', 'PretendController@show')->name('pretend.show');
    Route::post('/pretend', 'PretendController@login')->name('pretend.login');

    Route::get('/password', 'ResetController@show')->name('password');
    Route::put('/password/reset', 'ResetController@reset')->name('password.reset');
}); 

