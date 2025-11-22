<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Reviews Configuration
    |--------------------------------------------------------------------------
    |
    | Configuratie voor de Google Reviews widget in de header.
    | Update deze waardes wanneer je rating of aantal recensies wijzigt.
    |
    */

    // Google Reviews URL (je Google Business profiel link)
    'url' => env('GOOGLE_REVIEWS_URL', 'https://share.google/QTVi9GcCUpBvP7HJe'),

    // Gemiddelde rating (1.0 - 5.0)
    'rating' => env('GOOGLE_REVIEWS_RATING', 5),

    // Aantal recensies
    'count' => env('GOOGLE_REVIEWS_COUNT', 72),

    // Toon widget in header
    'enabled' => env('GOOGLE_REVIEWS_ENABLED', true),
];
