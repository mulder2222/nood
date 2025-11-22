<?php

namespace Webkul\SmartHome\Carriers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Shipping\Carriers\AbstractShipping;

/**
 * Custom Shipping Carrier: Free Shipping Above Threshold
 *
 * Shows free shipping when cart subtotal is above a configured threshold (default €150).
 * When available, this replaces the regular flat rate shipping option.
 */
class FreeAboveThreshold extends AbstractShipping
{
    /**
     * Shipping method carrier code.
     *
     * @var string
     */
    protected $code = 'freeabovethreshold';

    /**
     * Shipping method code.
     *
     * @var string
     */
    protected $method = 'freeabovethreshold_freeabovethreshold';

    /**
     * Calculate rate for free shipping above threshold.
     *
     * @return CartShippingRate|false
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $cart = Cart::getCart();
        $threshold = (float) $this->getConfigData('threshold') ?: 150.00;

        // Only show this method if cart subtotal is above threshold
        if ($cart->base_sub_total < $threshold) {
            return false;
        }

        return $this->getRate();
    }

    /**
     * Get rate.
     *
     * @return CartShippingRate
     */
    public function getRate(): CartShippingRate
    {
        $cartShippingRate = new CartShippingRate;

        $cartShippingRate->carrier = $this->getCode();
        $cartShippingRate->carrier_title = $this->getConfigData('title');
        $cartShippingRate->method = $this->getMethod();
        $cartShippingRate->method_title = $this->getConfigData('title');
        $cartShippingRate->method_description = $this->getConfigData('description');
        $cartShippingRate->price = 0;
        $cartShippingRate->base_price = 0;

        return $cartShippingRate;
    }
}
