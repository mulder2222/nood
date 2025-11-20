<?php

return [
    // Flat list of supported (potential) Stripe payment method types you want to allow selecting.
    // Keep only those you actually enable in your Stripe Dashboard.
    'payment_method_types' => [
        'card',
        'ideal',
        'bancontact',
        'sofort',
        'giropay',
        'eps',
        'p24',
        'multibanco',
        'link',
        'revolut_pay',
        // Add or remove as needed.
    ],
];
