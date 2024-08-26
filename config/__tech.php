<?php

// Response Codes & other global configurations
$techConfig = require app_path('Yantrana/__Laraware/Config/tech-config.php');

$techAppConfig = [
    /* Account related
    ------------------------------------------------------------------------- */
    'account' => [
        'expiry' => 24 * 2, // 48 Hours
        'password_reminder_expiry' => 24 * 2, // hours
        'app_password_reminder_expiry' => 2, // minutes
        'change_email_expiry' => 24 * 2, // hours
    ],

    /* Login Otp valid minutes
    ------------------------------------------------------------------------- */
    'otp_expiry' => 60 * 2,

    /* Email Config
    ------------------------------------------------------------------------- */
    'mail_from' => [
        env('MAIL_FROM_ADD', 'your@domain.com'),
        env('MAIL_FROM_NAME', 'E-Mail Service'),
    ],

    /* There is defined the key for social login providers
    ------------------------------------------------------------------------- */
    'social_login_driver' => [
        'via-facebook' => 'facebook',
        'via-google' => 'google',
    ],

    /* There is defined the key for social login providers
    ------------------------------------------------------------------------- */
    'social_login_driver_keys' => [
        'facebook' => 'via-facebook',
        'google' => 'via-google',
    ],

    /* Status Code Multiple Uses
    ------------------------------------------------------------------------- */
    'status_codes' => [
        0 => __tr('Inactive'), // in negative manner
        1 => __tr('Active'),
        2 => __tr('Inactive'),
        3 => __tr('Blocked'),
        4 => __tr('Never Activated'),
        5 => __tr('Soft Deleted'), // Archived
        6 => __tr('Suspended'),
        7 => __tr('On Hold'),
        8 => __tr('Completed'),
        9 => __tr('Invite'),
    ],
    'subscription_status' => [
        'active' => __tr('Active'),
        'cancelled' => __tr('Cancelled'),
        'pending' => __tr('Pending'),
        'initiated' => __tr('Initiated'),
    ],
    'subscription_methods' => [
        'auto' => __tr('Auto'),
        'manual' => __tr('Manual/Prepaid'),
    ],
    'subscription_payments_methods' => [
        'paypal' => __tr('PalPal'),
        'stripe' => __tr('Stripe'),
        'razorpay' => __tr('Razorpay'),
    ],
    /* Payment Status Code Multiple Uses
    ------------------------------------------------------------------------- */
    'payments' => [
        'methods' => [
            1 => ('PayPal'),
            2 => ('Stripe'),
            3 => ('Razorpay'),
            4 => __tr('Cash'),
        ],
        'status_codes' => [
            1 => __tr('Unpaid'), // PayPal IPN Payments
            2 => __tr('Paid'),
            3 => __tr('Failed'),
            4 => __tr('Pending'),
            5 => __tr('Refunded'),
        ],
        // status codes in which the amounts get debited from system transaction
        // if it already paid if any
        'debit_status_codes' => [
            1, 3, 4, 5,
        ],
        'payment_checkout_modes' => [
            1 => __tr('Test'),
            2 => __tr('Live'),
        ],
    ],

    "paypal_checkout_urls" => [
        "production" => "https://api-m.paypal.com",
        "sandbox" => "https://api-m.sandbox.paypal.com",
    ],
    
    /**
     * Make sure you find the items like {language_code} and add the newly added values
     */
    'contact_data_mapping' => [
        'dynamic_contact_full_name' => __tr('Contact Full Name'),
        'dynamic_contact_first_name' => __tr('Contact First Name'),
        'dynamic_contact_last_name' => __tr('Contact Last Name'),
        'dynamic_contact_wa_id' => __tr('Contact Phone'),
        'dynamic_contact_language_code' => __tr('Language Code'),
        'dynamic_contact_country' => __tr('Contact Country'),
        'dynamic_contact_email' => __tr('Contact Email'),
    ],
    'contact_custom_input_types' => [
        'text' => __tr('Text'),
        'number' => __tr('Number'),
        'email' => __tr('Email'),
        'url' => __tr('URL'),
        'date' => __tr('Date'),
        'time' => __tr('Time'),
        'datetime-local' => __tr('Date and Time Local'),
    ],
    'bot_reply_trigger_types' => [
        'welcome' => [
            'title' => __tr('Welcome'),
            'description' => __tr('First time message sender will get this message'),
            'priority_index' => 1,
        ],
        'is' => [
            'title' => __tr('Is'),
            'description' => __tr('It will trigger when message exactly match with trigger subject will match in sender message.'),
            'priority_index' => 2,
        ],
        'starts_with' => [
            'title' => __tr('Starts with'),
            'description' => __tr('It will trigger when message starts with trigger subject in sender message.'),
            'priority_index' => 3,
        ],
        'ends_with' => [
            'title' => __tr('Ends with'),
            'description' => __tr('It will trigger when message ends with trigger subject in sender message.'),
            'priority_index' => 4,
        ],
        'contains_word' => [
            'title' => __tr('Contains whole word'),
            'description' => __tr('It will trigger when contains subject will match in sender message.'),
            'priority_index' => 5,
        ],
        'contains' => [
            'title' => __tr('Contains'),
            'description' => __tr('It will trigger when contains subject will match in sender message'),
            'priority_index' => 6,
        ],
        'stop_promotional' => [
            'title' => __tr('Stop Promotional'),
            'description' => __tr('This trigger subject will stop promotional/marketing template messages using campaigns.'),
            'priority_index' => 7,
        ],
        'start_promotional' => [
            'title' => __tr('Start Promotional'),
            'description' => __tr('This trigger subject will start promotional/marketing template messages using campaigns.'),
            'priority_index' => 8,
        ],
    ],
    'demo_protected_bots' => env('DEMO_PROTECTED_BOTS', ''),
    /* Mail Drivers
    ------------------------------------------------------------------------- */
    'mail_drivers' => [
        'smtp' => [
            'id' => 'smtp',
            'name' => 'SMTP',
            'config_data' => [
                'port' => 'smtp_mail_port',
                'host' => 'smtp_mail_host',
                'username' => 'smtp_mail_username',
                'encryption' => 'smtp_mail_encryption',
                'password' => 'smtp_mail_password_or_apikey',
            ],
        ],
        'sparkpost' => [
            'id' => 'sparkpost',
            'name' => 'Sparkpost',
            'config_data' => [
                'sparkpost_mail_password_or_apikey',
            ],
        ],
        'mailgun' => [
            'id' => 'mailgun',
            'name' => 'Mailgun',
            'config_data' => [
                'mailgun_domain',
                'mailgun_mail_password_or_apikey',
                'mailgun_endpoint',
            ],
        ],
    ],

    /* Mail encryption types
    ------------------------------------------------------------------------- */
    'mail_encryption_types' => [
        'ssl' => 'SSL',
        'tls' => 'TLS',
        'starttls' => 'STARTTLS',
    ],

    /* Define logo name of application
    ------------------------------------------------------------------------- */
    'logo_name' => 'logo.svg',

    /* Define small logo name of application
    ------------------------------------------------------------------------- */
    'small_logo_name' => 'logo-short.svg',

    /* Define favicon name of application
    ------------------------------------------------------------------------- */
    'favicon_name' => 'favicon.png',

    /* Default paginate count
    ------------------------------------------------------------------------- */
    'paginate_count' => 12,

    /*
        Default translations
    */
    'default_translation_language' => [
        'id' => 'en',
        'name' => 'English',
        'is_rtl' => false,
        'status' => true,
    ],
];

$appTechConfig = [];
if (file_exists(base_path('user-tech-config.php'))) {
    $appTechConfig = require base_path('user-tech-config.php');
}

return array_merge($techConfig, $techAppConfig, $appTechConfig);
