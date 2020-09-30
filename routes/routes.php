<?php

Route::middleware(['web'])->group(function () {
    Route::group(['prefix' => 'dummy-settings'], function () {
        Route::get('/','\Manuel90\DummySettings\Http\Controller@index')->name('dummysettings.index');
        Route::post('/send-email','\Manuel90\DummySettings\Http\Controller@sendEmail')->name('dummysettings.sending');

        Route::get('/assets','\Manuel90\DummySettings\Http\Controller@assets')->name('dummysettings.assets');

        Route::group(['prefix' => 'ajax/v1'], function () {
            Route::post('/store_setting','\Manuel90\DummySettings\Http\Controller@saveGeneralSetting')->name('dummysettings.store_custom_setting');
        });
    });
});

Route::prefix('api/dummy-settings/ajax/v1')->group(function () {
    Route::get('/settings','\Manuel90\DummySettings\Http\Controller@settings')->name('dummysettings.settings');
});