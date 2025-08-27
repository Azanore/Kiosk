<?php
return [
    'env' => 'dev', // 'dev' or 'prod'
    'timezone' => 'Africa/Casablanca',
    'locale' => 'fr_MA',

    // Kiosk behavior
    'kiosk_idle_seconds' => 90,
    'confirm_return_seconds' => 12,
    'auto_cancel_minutes' => 15,

    // Formatting
    'currency_suffix' => 'DH',
    'number_locale' => 'fr_FR',

    // Café details (for receipt header)
    'cafe_name' => 'Café Marocain',
    'cafe_address' => '123 Avenue Mohammed V, Casablanca',
    'cafe_phone' => '+212 5XX XX XX XX',

    // Payment provider: 'simulator' (default) or 'terminal' (polling flow)
    'payment_provider' => 'simulator',
];
