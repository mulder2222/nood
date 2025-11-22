<?php

namespace Webkul\SmartHome\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Stripe\PaymentMethodConfiguration;
use Stripe\Stripe;
use Stripe\StripeObject;

class StripeMethodsController extends Controller
{
    /**
     * Determine if a specific payment method is available in the given configuration.
     */
    protected function isMethodAvailable($config, string $code): bool
    {
        if (! $config) {
            return false;
        }

        // Some SDK versions always expose a StripeObject for each method; rely on `available` flag.
        $value = $config->$code ?? null;

        if ($value instanceof StripeObject) {
            return (bool) ($value->available ?? false);
        }

        return false;
    }

    /**
     * Return enabled Stripe payment methods for the account (default configuration).
     */
    public function index(): JsonResponse
    {
        $apiKey = core()->getConfigData('sales.payment_methods.stripe.stripe_api_key');

        if (! $apiKey) {
            return response()->json(['methods' => []]);
        }

        Stripe::setApiKey($apiKey);

        try {
            $configs = PaymentMethodConfiguration::all(['limit' => 10]);
        } catch (\Throwable $e) {
            return response()->json(['methods' => []]);
        }

        $config = null;
        foreach ($configs as $c) {
            if (isset($c->is_default) && $c->is_default) {
                $config = $c;
                break;
            }
        }

        if (! $config) {
            $first = $configs->data[0] ?? null;
            if ($first instanceof PaymentMethodConfiguration) {
                $config = $first;
            }
        }

        if (! $config) {
            return response()->json(['methods' => []]);
        }

        // List of known Checkout-supported payment methods to filter output and labels.
        $labels = [
            'card'               => 'Card',
            'link'               => 'Link',
            'sepa_debit'         => 'SEPA Debit',
            'bacs_debit'         => 'Bacs Direct Debit',
            'au_becs_debit'      => 'BECS Direct Debit (AU)',
            'us_bank_account'    => 'US Bank Account (ACH)',
            'ideal'              => 'iDEAL',
            'bancontact'         => 'Bancontact',
            'sofort'             => 'Sofort',
            'giropay'            => 'giropay',
            'eps'                => 'EPS',
            'p24'                => 'Przelewy24',
            'blik'               => 'BLIK',
            'alipay'             => 'Alipay',
            'klarna'             => 'Klarna',
            'afterpay_clearpay'  => 'Clearpay / Afterpay',
            'affirm'             => 'Affirm',
            'cashapp'            => 'Cash App Pay',
            'paypal'             => 'PayPal',
            'grabpay'            => 'GrabPay',
            'wechat_pay'         => 'WeChat Pay',
            'konbini'            => 'Konbini',
            'oxxo'               => 'OXXO',
            'boleto'             => 'Boleto',
            'paynow'             => 'PayNow',
            'promptpay'          => 'PromptPay',
            'multibanco'         => 'Multibanco',
        ];

        $methods = [];
        foreach ($labels as $code => $label) {
            if ($this->isMethodAvailable($config, $code)) {
                $methods[] = [
                    'code'  => $code,
                    'label' => $label,
                ];
            }
        }

        // Ensure card is first if present.
        usort($methods, function ($a, $b) {
            if ($a['code'] === 'card') return -1;
            if ($b['code'] === 'card') return 1;
            return strcmp($a['label'], $b['label']);
        });

        return response()->json(['methods' => $methods]);
    }

    /**
     * Return all known Stripe methods with an `enabled` flag for the current default configuration.
     */
    public function all(): JsonResponse
    {
        $apiKey = core()->getConfigData('sales.payment_methods.stripe.stripe_api_key');

        // Known Checkout-supported payment methods and labels (superset; matches index()).
        $labels = [
            'card'               => 'Card',
            'link'               => 'Link',
            'sepa_debit'         => 'SEPA Debit',
            'bacs_debit'         => 'Bacs Direct Debit',
            'au_becs_debit'      => 'BECS Direct Debit (AU)',
            'us_bank_account'    => 'US Bank Account (ACH)',
            'ideal'              => 'iDEAL',
            'bancontact'         => 'Bancontact',
            'sofort'             => 'Sofort',
            'giropay'            => 'giropay',
            'eps'                => 'EPS',
            'p24'                => 'Przelewy24',
            'blik'               => 'BLIK',
            'alipay'             => 'Alipay',
            'klarna'             => 'Klarna',
            'afterpay_clearpay'  => 'Clearpay / Afterpay',
            'affirm'             => 'Affirm',
            'cashapp'            => 'Cash App Pay',
            'paypal'             => 'PayPal',
            'grabpay'            => 'GrabPay',
            'wechat_pay'         => 'WeChat Pay',
            'konbini'            => 'Konbini',
            'oxxo'               => 'OXXO',
            'boleto'             => 'Boleto',
            'paynow'             => 'PayNow',
            'promptpay'          => 'PromptPay',
            'multibanco'         => 'Multibanco',
        ];

        // Default to all disabled when no API key/config; still return full list for visibility.
        if (! $apiKey) {
            $all = [];
            foreach ($labels as $code => $label) {
                $all[] = [ 'code' => $code, 'label' => $label, 'enabled' => false ];
            }

            return response()->json(['methods' => $all]);
        }

        Stripe::setApiKey($apiKey);

        $config = null;
        try {
            $configs = PaymentMethodConfiguration::all(['limit' => 10]);

            foreach ($configs as $c) {
                if (isset($c->is_default) && $c->is_default) {
                    $config = $c; break;
                }
            }
            if (! $config) {
                $first = $configs->data[0] ?? null;
                if ($first instanceof PaymentMethodConfiguration) {
                    $config = $first;
                }
            }
        } catch (\Throwable $e) {
            $config = null;
        }
    // Configuration resolved (or null on error).

        $enabledOnly = [];
        foreach ($labels as $code => $label) {
            if ($this->isMethodAvailable($config, $code)) {
                $enabledOnly[] = [
                    'code'    => $code,
                    'label'   => $label,
                    'enabled' => true,
                ];
            }
        }

        // Keep card first among enabled set.
        usort($enabledOnly, function ($a, $b) {
            if ($a['code'] === 'card') return -1;
            if ($b['code'] === 'card') return 1;
            return strcmp($a['label'], $b['label']);
        });

        return response()->json(['methods' => $enabledOnly]);
    }
}
