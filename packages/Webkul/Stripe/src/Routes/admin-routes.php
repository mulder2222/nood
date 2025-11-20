<?php

use Illuminate\Support\Facades\Route;
use Webkul\Stripe\Http\Controllers\Admin\StripeController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/stripe'], function () {
    Route::controller(StripeController::class)->group(function () {
        Route::get('', 'index')->name('admin.stripe.index');
    });
});
