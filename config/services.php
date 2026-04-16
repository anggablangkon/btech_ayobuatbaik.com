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

    "postmark" => [
        "token" => env("POSTMARK_TOKEN"),
    ],

    "ses" => [
        "key" => env("AWS_ACCESS_KEY_ID"),
        "secret" => env("AWS_SECRET_ACCESS_KEY"),
        "region" => env("AWS_DEFAULT_REGION", "us-east-1"),
    ],

    "resend" => [
        "key" => env("RESEND_KEY"),
    ],

    "slack" => [
        "notifications" => [
            "bot_user_oauth_token" => env("SLACK_BOT_USER_OAUTH_TOKEN"),
            "channel" => env("SLACK_BOT_USER_DEFAULT_CHANNEL"),
        ],
    ],
    "midtrans" => [
        "serverKey" => env("MIDTRANS_SERVERKEY"),
        "clientKey" => env("MIDTRANS_CLIENTKEY"),
        "is_production" => env("MIDTRANS_IS_PRODUCTION", false),
        "is_sanitized" => env("MIDTRANS_IS_SANITIZED", true),
        "is_3ds" => env("MIDTRANS_IS_3DS", true),
        "fee_estimator" => [
            "percentage_rates" => [
                "qris" => (float) env("MIDTRANS_QRIS_FEE_RATE", 0.007),
                "gopay" => (float) env("MIDTRANS_GOPAY_FEE_RATE", 0.02),
                "shopeepay" => (float) env("MIDTRANS_SHOPEEPAY_FEE_RATE", 0.02),
            ],
            "flat_fees" => [
                "echannel" => (int) env("MIDTRANS_ECHANNEL_FEE", 4400),
                "bank_transfer" => (int) env("MIDTRANS_BANK_TRANSFER_FEE", 4400),
            ],
        ],
    ],
    "google" => [
        "client_id" => env("GOOGLE_CLIENT_ID"),
        "client_secret" => env("GOOGLE_CLIENT_SECRET"),
        "redirect" => env("GOOGLE_REDIRECT_URI"),
    ],
    "meta" => [
        "pixel_id" => env("META_PIXEL_ID"),
        "access_token" => env("META_PIXEL_ACCESS_TOKEN"),
    ],
    "fonnte" => [
        "token" => env("FONNTE_API_KEY"),
    ],
    "donation" => [
        "followup_days" => env("DONATION_FOLLOWUP_DAYS", 3),
    ],
];
