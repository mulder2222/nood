<?php

use Illuminate\Support\Facades\Route;
use BaWe\ProsCons\Http\Controllers\Admin\ProsConsController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin'], function () {
    Route::get('/pros-cons/edit/{productId}', [ProsConsController::class, 'edit'])
        ->name('admin.proscons.edit');

    Route::post('/pros-cons/edit/{productId}', [ProsConsController::class, 'update'])
        ->name('admin.proscons.update');
});
