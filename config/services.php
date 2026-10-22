<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'unwim' => [
        'client_id' => env('UNWIM_CLIENT_ID'),
        'client_secret' => env('UNWIM_CLIENT_SECRET'),
        'redirect' => env('UNWIM_REDIRECT_URI'),
    ],

    // Shared whatsapp-gateway service (Baileys), also used by blaster.dev.unwim.ac.id.
    // See /home/devunwim/domains/blaster.dev.unwim.ac.id/public_html/README.md for the API.
    'wa_service' => [
        'url'     => env('WA_SERVICE_URL', 'http://127.0.0.1:3300'),
        'secret'  => env('WA_SERVICE_SECRET', ''),
        'enabled' => env('WA_NOTIFICATIONS_ENABLED', true),
    ],

    // WhatsApp Blaster API v1 (API key per pengguna, pengiriman lewat antrean).
    'wa_blaster' => [
        'url'     => env('WA_BLASTER_URL', 'https://blaster.dev.unwim.ac.id/v1'),
        'key'     => env('WA_BLASTER_KEY', ''),
        'account' => env('WA_BLASTER_ACCOUNT', ''),
        // Beberapa nomor pengirim (id akun Blaster, pisahkan koma). Kosong = pakai 'account' di atas.
        'accounts' => env('WA_BLASTER_ACCOUNTS', ''),
    ],

];
