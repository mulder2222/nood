<?php

namespace Webkul\SmartHome\Carriers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Shipping\Carriers\AbstractShipping;

/**
 * Custom FlatRate Carrier - Smart Home Package
 *
 * This carrier extends the standard Bagisto FlatRate with smart logic:
 * - Hides itself when FreeAboveThreshold is available (cart >= €150)
 * - Prevents showing paid shipping when free shipping is an option
 * - Improves checkout UX by showing only relevant shipping options
 */
class FlatRate extends AbstractShipping
{
    /**
     * Shipping method carrier code.
     *
     * @var string
     */
    protected $code = 'flatrate';

    /**
     * Shipping method code.
     *
     * @var string
     */
    protected $method = 'flatrate_flatrate';

    /**
     * Calculate rate for flatrate.
     *
     * @return \Webkul\Checkout\Models\CartShippingRate|false
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        // Smart Logic: Hide Flat Rate when FreeAboveThreshold is available
        if ($this->shouldHideDueToFreeShipping()) {
            return false;
        }

        return $this->getRate();
    }

    /**
     * Check if Flat Rate should be hidden due to free shipping availability.
     *
     * @return bool
     */
    protected function shouldHideDueToFreeShipping(): bool
    {
        $cart = Cart::getCart();

        if (! $cart) {
            return false;
        }

        // Check if FreeAboveThreshold carrier is active
        $freeAboveThresholdActive = (bool) core()->getConfigData('sales.carriers.freeabovethreshold.active');

        if (! $freeAboveThresholdActive) {
            return false; // FreeAboveThreshold disabled, show Flat Rate normally
        }

        // Get threshold amount
        $threshold = (float) core()->getConfigData('sales.carriers.freeabovethreshold.threshold') ?: 150.00;

        // Check if cart subtotal meets threshold
        if ($cart->base_sub_total >= $threshold) {
            // Cart is above threshold, free shipping is available
            // Hide Flat Rate to avoid confusing customers
            return true;
        }

        return false; // Cart below threshold, show Flat Rate normally
    }

    /**
     * Get rate.
     *
     * @return CartShippingRate
     */
    public function getRate(): CartShippingRate
    {
        $cart = Cart::getCart();

        $cartShippingRate = new CartShippingRate;

        $cartShippingRate->carrier = $this->getCode();
        $cartShippingRate->carrier_title = $this->getConfigData('title');
        $cartShippingRate->method = $this->getMethod();
        $cartShippingRate->method_title = $this->getConfigData('title');
        $cartShippingRate->method_description = $this->getConfigData('description');
        $cartShippingRate->price = 0;
        $cartShippingRate->base_price = 0;

        if ($this->getConfigData('type') == 'per_unit') {
            foreach ($cart->items as $item) {
                if ($item->getTypeInstance()->isStockable()) {
                    $cartShippingRate->price += core()->convertPrice($this->getConfigData('default_rate')) * $item->quantity;
                    $cartShippingRate->base_price += $this->getConfigData('default_rate') * $item->quantity;
                }
            }
        } else {
            $cartShippingRate->price = core()->convertPrice($this->getConfigData('default_rate'));
            $cartShippingRate->base_price = $this->getConfigData('default_rate');
        }

        return $cartShippingRate;
    }
}
