<?php

namespace Webkul\Stripe\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;

class PaymentController extends Controller
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
    ) {}

    /**
     * Redirects to the Stripe server.
     */
    public function redirect(): RedirectResponse
    {
        $cart = Cart::getCart();

        Stripe::setApiKey(core()->getConfigData('sales.payment_methods.stripe.stripe_api_key'));

        $allowedTypes = null;
        $stripeChoice = session('stripe_submethod');
        $available = config('stripe.payment_method_types', []);
        if ($stripeChoice && in_array($stripeChoice, $available, true)) {
            $allowedTypes = [$stripeChoice];
        }

        $payload = [
            'line_items' => [[
                'price_data' => [
                    'currency'     => strtolower($cart->global_currency_code ?: $cart->cart_currency_code ?: 'eur'),
                    'product_data' => [
                        'name' => 'Stripe Checkout Payment order id - ' . $cart->id,
                    ],
                    'unit_amount'  => (int) round($cart->grand_total * 100),
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url'  => route('stripe.cancel'),
            'metadata'    => [
                'cart_id' => (string) $cart->id,
            ],
        ];

        // Prefill customer data to skip Stripe data entry steps
        $billingAddress = $cart->billing_address;
        $customer = auth()->guard('customer')->check() ? auth()->guard('customer')->user() : null;

        $email = $billingAddress?->email ?: $customer?->email;
        $firstName = $billingAddress?->first_name ?: $customer?->first_name;
        $lastName = $billingAddress?->last_name ?: $customer?->last_name;

        if ($email && ($firstName || $lastName)) {
            $fullName = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));

            if ($fullName && $billingAddress) {
                // Build address array
                $addressLine1 = '';
                if (is_array($billingAddress->address1)) {
                    $addressLine1 = implode(', ', array_filter($billingAddress->address1));
                } elseif (!empty($billingAddress->address1)) {
                    $addressLine1 = $billingAddress->address1;
                }

                $addressData = [
                    'line1' => $addressLine1 ?: 'N/A',
                    'city' => $billingAddress->city ?? '',
                    'postal_code' => $billingAddress->postcode ?? '',
                    'country' => $billingAddress->country ?? 'NL',
                ];

                if (!empty($billingAddress->state)) {
                    $addressData['state'] = $billingAddress->state;
                }

                // Filter out empty values except line1 which is required
                $addressData = array_filter($addressData, function($value, $key) {
                    return ($key === 'line1' || ($value !== '' && $value !== null));
                }, ARRAY_FILTER_USE_BOTH);

                // Create Stripe customer with full billing details
                $customerData = [
                    'email' => $email,
                    'name' => $fullName,
                    'address' => $addressData,
                ];

                try {
                    $stripeCustomer = \Stripe\Customer::create($customerData);
                    $payload['customer'] = $stripeCustomer->id;

                    // Allow Stripe to auto-update customer with new details if needed
                    $payload['customer_update'] = [
                        'name' => 'auto',
                        'address' => 'auto',
                    ];

                    // Add invoice_creation to automatically use customer details
                    $payload['invoice_creation'] = [
                        'enabled' => true,
                        'invoice_data' => [
                            'description' => 'Order #' . $cart->id,
                        ],
                    ];

                    // Log success for debugging
                    Log::info('Stripe customer created successfully', [
                        'customer_id' => $stripeCustomer->id,
                        'name' => $fullName,
                        'email' => $email,
                        'address' => $addressData,
                    ]);
                } catch (\Exception $e) {
                    // If customer creation fails, fall back to customer_email
                    $payload['customer_email'] = $email;
                    // Log error for debugging
                    Log::error('Stripe customer creation failed: ' . $e->getMessage(), [
                        'customer_data' => $customerData,
                    ]);
                }
            }
        } elseif ($email) {
            // If we only have email, use that
            $payload['customer_email'] = $email;
        }

        if ($allowedTypes) {
            $payload['payment_method_types'] = $allowedTypes;
        }

        $session = CheckoutSession::create($payload);

        return redirect()->away($session->url);
    }

    /**
     * Place an order and redirect to the success page.
     */
    public function success(): RedirectResponse
    {
        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        /** @var \Webkul\Sales\Models\Order $order */
        $order = $this->orderRepository->create($data);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Cancel/failure handler: just go back to cart for now.
     */
    public function failure(): RedirectResponse
    {
        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Prepare invoice payload.
     */
    protected function prepareInvoiceData($order): array
    {
        $invoiceData = [
            'order_id' => $order->id,
            'invoice'  => ['items' => []],
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    /**
     * Return enabled Stripe sub payment methods (config based) for frontend.
     * Response shape: { methods: [ { code, label, enabled } ] }
     */
    public function available(): array
    {
        return [
            'methods' => $this->mapMethods(config('stripe.payment_method_types', []), true),
        ];
    }

    /**
     * Return full Stripe sub payment method list (currently same as available, placeholder for future dynamic enabling).
     * Kept separate because the checkout component calls `/api/stripe/methods/all` expecting possibly disabled items.
     */
    public function availableAll(): array
    {
        // For now everything in config is considered enabled=true. Later we can enrich with live Stripe availability.
        return [
            'methods' => $this->mapMethods(config('stripe.payment_method_types', []), true),
        ];
    }

    /**
     * Helper to map method codes to label + enabled flag.
     *
     * @param array<int,string> $codes
     * @param bool $enabled Default enabled status (can be overridden later per code)
     * @return array<int,array{code:string,label:string,enabled:bool}>
     */
    protected function mapMethods(array $codes, bool $enabled = true): array
    {
        $labels = $this->labelMap();

        return array_map(function (string $code) use ($labels, $enabled) {
            return [
                'code'    => $code,
                'label'   => $labels[$code] ?? ucfirst(str_replace(['_', '-'], ' ', $code)),
                'enabled' => $enabled,
            ];
        }, $codes);
    }

    /**
     * Central label mapping (duplicate of OnepageController private map trimmed for maintainability).
     */
    protected function labelMap(): array
    {
        return [
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
    }
}
