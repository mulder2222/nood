<?php

namespace Webkul\Stripe\Http\Controllers\Shop;

use Illuminate\View\View;
use Webkul\Shop\Http\Controllers\Controller;

class StripeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('stripe::shop.index');
    }
}
