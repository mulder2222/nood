<?php

namespace Webkul\SmartHome\Http\Requests;

use Webkul\Shop\Http\Requests\CartAddressRequest as BaseCartAddressRequest;

class CartAddressRequest extends BaseCartAddressRequest
{
    /**
     * Merge new address rules.
     * Override to make state field always optional for SmartHome theme.
     */
    private function mergeAddressRules(string $addressType): void
    {
        $this->mergeWithRules([
            "{$addressType}.company_name" => ['nullable'],
            "{$addressType}.first_name"   => ['required'],
            "{$addressType}.last_name"    => ['required'],
            "{$addressType}.email"        => ['required'],
            "{$addressType}.address"      => ['required', 'array', 'min:1'],
            "{$addressType}.city"         => ['required'],
            "{$addressType}.country"      => core()->isCountryRequired() ? ['required'] : ['nullable'],
            "{$addressType}.state"        => ['nullable'], // Always nullable for SmartHome theme
            "{$addressType}.postcode"     => core()->isPostCodeRequired() ? ['required', new \Webkul\Core\Rules\PostCode] : [new \Webkul\Core\Rules\PostCode],
            "{$addressType}.phone"        => ['required', new \Webkul\Core\Rules\PhoneNumber],
        ]);

        if ($addressType == 'billing') {
            $this->mergeWithRules([
                "{$addressType}.vat_id" => [(new \Webkul\Customer\Rules\VatIdRule)->setCountry($this->input('billing.country'))],
            ]);
        }
    }

    /**
     * Merge additional rules.
     */
    private function mergeWithRules($rules): void
    {
        $this->rules = array_merge($this->rules, $rules);
    }
}
