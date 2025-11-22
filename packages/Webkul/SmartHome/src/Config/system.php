<?php

/**
 * SmartHome Package - Admin Configuration
 *
 * This configuration extends Bagisto's admin system configuration
 * with custom shipping methods for the Smart Home shop.
 */

return [
    /**
     * Free Shipping Above Threshold Configuration
     *
     * Provides free shipping when cart subtotal exceeds a threshold.
     * Automatically replaces paid shipping options when conditions are met.
     */
    [
        'key'    => 'sales.carriers.freeabovethreshold',
        'name'   => 'Gratis verzending boven drempel',
        'info'   => 'Bied gratis verzending aan wanneer het winkelwagen subtotaal een bepaald bedrag overschrijdt. Deze optie vervangt automatisch betaalde verzendopties.',
        'sort'   => 3,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'Titel',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'description',
                'title'         => 'Beschrijving',
                'type'          => 'textarea',
                'depends'       => 'active:1',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'threshold',
                'title'         => 'Drempelbedrag (€)',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1|numeric|min:0',
                'channel_based' => true,
                'locale_based'  => false,
                'info'          => 'Minimale bestelbedrag voor gratis verzending (bijv. 150 voor €150)',
            ],
            [
                'name'          => 'active',
                'title'         => 'Status',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ],
        ],
    ],
];
