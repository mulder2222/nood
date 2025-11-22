<?php

use Illuminate\Support\Facades\Route;
use Webkul\SmartHome\Http\Controllers\StripeMethodsController;

Route::group(['prefix' => 'api'], function () {
    Route::get('stripe/methods', [StripeMethodsController::class, 'index'])->name('smarthome.stripe.methods');
    Route::get('stripe/methods/all', [StripeMethodsController::class, 'all'])->name('smarthome.stripe.methods.all');
});
