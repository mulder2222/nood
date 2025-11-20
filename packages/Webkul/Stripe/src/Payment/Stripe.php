<?php

namespace Webkul\Stripe\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Payment\Payment\Payment;

class Stripe extends Payment
{
    /**
     * Payment method code.
     */
    protected string $code = 'stripe';

    /**
     * Return redirect URL that will kick off the Stripe flow.
     */
    public function getRedirectUrl(): string
    {
        return route('stripe.process');
    }

    /**
     * Returns payment method image.
     */
    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/money-transfer.png', 'shop');
    }
}
