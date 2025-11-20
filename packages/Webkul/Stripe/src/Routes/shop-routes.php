<?php

use Illuminate\Support\Facades\Route;
use Webkul\Stripe\Http\Controllers\PaymentController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    // Start Stripe payment (redirect to Stripe)
    Route::get('/stripe-redirect', [PaymentController::class, 'redirect'])->name('stripe.process');

    // Handle success return from Stripe
    Route::get('/stripe-success', [PaymentController::class, 'success'])->name('stripe.success');

    // Handle cancel/failure
    Route::get('/stripe-cancel', [PaymentController::class, 'failure'])->name('stripe.cancel');

    // Public (web) JSON endpoints used by checkout Vue component to populate Stripe submethods
    Route::get('/api/stripe/methods', [PaymentController::class, 'available']);
    Route::get('/api/stripe/methods/all', [PaymentController::class, 'availableAll']);
});
