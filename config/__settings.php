<?php

return [

    /* Configuration setting data-types id
    ------------------------------------------------------------------------- */
    'datatypes' => [
        'string' => 1,
        'bool' => 2,
        'int' => 3,
        'json' => 4,
    ],

    /* Configuration Setting Items
    ------------------------------------------------------------------------- */
    'items' => [
        'general' => [
            'logo_name' => [
                'key' => 'logo_name',
                'data_type' => 1,    // string,
                'placeholder' => '',
                'default' => 'logo.svg',
                'ignore_empty' => true,
            ],
            'small_logo_name' => [
                'key' => 'small_logo_name',
                'data_type' => 1,    // string,
                'placeholder' => '',
                'default' => '',
                'ignore_empty' => true,
            ],
            'favicon_name' => [
                'key' => 'favicon_name',
                'data_type' => 1,    // string,
                'placeholder' => '',
                'default' => 'favicon.png',
                'ignore_empty' => true,
            ],
            'name' => [
                'key' => 'name',
                'data_type' => 1,    // string,
                'placeholder' => 'Your Website Name',
                'default' => 'Your Company Name',
                /* 'validation_rules'   => [
                    'min:0'
                ] */
            ],
            'description' => [
                'key' => 'description',
                'data_type' => 1, // string,
                'placeholder' => 'Your Website description',
                'default' => '',
            ],
            'contact_email' => [
                'key' => 'contact_email',
                'data_type' => 1,    // string
                'placeholder' => 'your-email-address@example.com',
                'default' => 'your-contact-email@domain.com',
            ],
            'contact_details' => [
                'key' => 'contact_details',
                'data_type' => 1,    // string
                'placeholder' => __tr('place your contact details'),
                'default' => '',
                'ignore_empty' => true,
            ],
            'default_language' => [
                'key' => 'default_language',
                'data_type' => 1,    // string
                'default' => config('__tech.default_translation_language.id', 'en'),
            ],
            'timezone' => [
                'key' => 'timezone',
                'data_type' => 1,    // string
                'default' => 'UTC',
            ],
        ],
        'user' => [
            'enable_vendor_registration' => [
                'key' => 'enable_vendor_registration',
                'data_type' => 2,    // bool
                'default' => 1,
            ],
            'message_for_disabled_registration' => [
                'key' => 'message_for_disabled_registration',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => false, // it won't encrypt values
                'ignore_empty' => false, // it will allow blank entry
                'validation_rules' => [
                ],
            ],
            'activation_required_for_new_user' => [
                'key' => 'activation_required_for_new_user',
                'data_type' => 2,    // bool
                'default' => false,
            ],
            // welcome email
            'send_welcome_email' => [
                'key' => 'send_welcome_email',
                'data_type' => 2,    // bool
                'default' => false,
            ],
            'welcome_email_content' => [
                'key' => 'welcome_email_content',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => false, // it won't encrypt values
                'ignore_empty' => false, // it will allow blank entry
                'validation_rules' => [
                    'required_if:send_welcome_email,on',
                ],
            ],
            'disallow_disposable_emails' => [
                'key' => 'disallow_disposable_emails',
                'data_type' => 2, // boolean
                'default' => false,
            ],
            'user_terms' => [
                'key' => 'user_terms',
                'data_type' => 1,    // string
                'default' => '',
            ],
            'vendor_terms' => [
                'key' => 'vendor_terms',
                'data_type' => 1,    // string
                'default' => '',
            ],
            'privacy_policy' => [
                'key' => 'privacy_policy',
                'data_type' => 1,    // string
                'default' => '',
            ],
        ],
        'currency' => [
            'currency_format' => [
                'key' => 'currency_format',
                'data_type' => 1,    // string
                'default' => '{__currencySymbol__}{__amount__} {__currencyCode__}',
            ],
            // Currency settings
            'currency' => [
                'key' => 'currency',
                'data_type' => 1,    // string
                'default' => 'USD',
            ],
            'currency_symbol' => [
                'key' => 'currency_symbol',
                'data_type' => 1,    // string
                'default' => '&#36;',
            ],
            'currency_value' => [
                'key' => 'currency_value',
                'data_type' => 1,    // string
                'default' => 'USD',
            ],
            'round_zero_decimal_currency' => [
                'key' => 'round_zero_decimal_currency',
                'data_type' => 2, // boolean
                'default' => true, // round
            ],
        ],
        'upi_payment' => [
            // Payment method
            'enable_upi_payment' => [
                'key' => 'enable_upi_payment',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'payment_upi_address' => [
                'key' => 'payment_upi_address',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => __tr('UPI Address'),
                'hide_value' => false,
                'ignore_empty' => false,
                'validation_rules' => [
                    'required_if:enable_upi_payment,on',
                    'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+$/'
                ],
            ],
            'payment_upi_customer_notes' => [
                'key' => 'payment_upi_customer_notes',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => __tr('Notes for the customers'),
                'hide_value' => false,
                'ignore_empty' => false,
                'validation_rules' => [
                ],
            ],
        ],
        'bank_transfer' => [
            // Payment method
            'enable_bank_transfer' => [
                'key' => 'enable_bank_transfer',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'bank_transfer_instructions' => [
                'key' => 'bank_transfer_instructions',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => __tr('UPI Address'),
                'hide_value' => false,
                'ignore_empty' => false,
                'validation_rules' => [
                    'required_if:enable_upi_payment,on',
                ],
            ],
            'payment_upi_customer_notes' => [
                'key' => 'payment_upi_customer_notes',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => __tr('Notes for the customers'),
                'hide_value' => false,
                'ignore_empty' => false,
                'validation_rules' => [
                ],
            ],
        ],
        'paypal_payment' => [
            'enable_paypal' => [
                'key' => 'enable_paypal',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'paypal_checkout_testing_publishable_key' => [
                'key' => 'paypal_checkout_testing_publishable_key',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => __tr('Test Publishable key'),
                'hide_value' => false,
                'ignore_empty' => false,
            ],
            'paypal_checkout_testing_secret_key' => [
                'key' => 'paypal_checkout_testing_secret_key',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => __tr('Test Secret Key'),
                'hide_value' => false,
                'ignore_empty' => false,
            ],
            'paypal_checkout_live_secret_key' => [
                'key' => 'paypal_checkout_live_secret_key',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => 'Live secret key',
                'hide_value' => false,
                'ignore_empty' => false,
            ],
            'paypal_checkout_live_publishable_key' => [
                'key' => 'paypal_checkout_live_publishable_key',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => 'Live Publishable Key',
                'hide_value' => false,
                'ignore_empty' => false,
            ],
            'use_test_paypal_checkout' => [
                'key' => 'use_test_paypal_checkout',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
        ],
        'payment' => [
            // Payment method
            'enable_stripe' => [
                'key' => 'enable_stripe',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'stripe_enable_calculate_taxes' => [
                'key' => 'stripe_enable_calculate_taxes',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'stripe_enable_invoice_list' => [
                'key' => 'stripe_enable_invoice_list',
                'data_type' => 2,    // boolean
                'default' => true,
            ],
            'use_test_stripe' => [
                'key' => 'use_test_stripe',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'stripe_testing_secret_key' => [
                'key' => 'stripe_testing_secret_key',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => __tr('Test Secret Key'),
                'hide_value' => true,
                'ignore_empty' => true,
            ],
            'stripe_testing_publishable_key' => [
                'key' => 'stripe_testing_publishable_key',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => __tr('Test Publishable key'),
                'hide_value' => true,
                'ignore_empty' => true,
            ],
            'stripe_testing_webhook_secret' => [
                'key' => 'stripe_testing_webhook_secret',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => 'Stripe Test Webhook Secret',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    // 'required'
                ],
            ],
            'stripe_live_secret_key' => [
                'key' => 'stripe_live_secret_key',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => 'Live secret key',
                'hide_value' => true,
                'ignore_empty' => true,
            ],
            'stripe_live_publishable_key' => [
                'key' => 'stripe_live_publishable_key',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => 'Live Publishable Key',
                'hide_value' => true,
                'ignore_empty' => true,
            ],
            'stripe_live_webhook_secret' => [
                'key' => 'stripe_live_webhook_secret',
                'data_type' => 1,    // string
                'default' => '',
                'placeholder' => 'Stripe Live Webhook Secret',
                'hide_value' => true,
                'validation_rules' => [
                    // 'required'
                ],
                'ignore_empty' => true,
            ],
        ],
        'pusher' => [
            'broadcast_connection_driver' => [
                'key' => 'broadcast_connection_driver',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => 'pusher',
            ],
            'pusher_app_id' => [
                'key' => 'pusher_app_id',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                ],
            ],
            'pusher_app_key' => [
                'key' => 'pusher_app_key',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                ],
            ],
            'pusher_app_secret' => [
                'key' => 'pusher_app_secret',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                ],
            ],
            'pusher_app_cluster' => [
                'key' => 'pusher_app_cluster',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                ],
            ],
            // soketi specific
            'pusher_app_host' => [
                'key' => 'pusher_app_host',
                'data_type' => 1,     // string
                'default' => '127.0.0.1',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required_if:broadcast_connection_driver,soketi'
                ],
            ],
            'pusher_app_port' => [
                'key' => 'pusher_app_port',
                'data_type' => 3,     // int
                'default' => 6001,
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'nullable',
                    'integer',
                    'required_if:broadcast_connection_driver,soketi'
                ],
            ],
            'pusher_app_scheme' => [
                'key' => 'pusher_app_scheme',
                'data_type' => 1,     // string
                'default' => 'https',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required_if:broadcast_connection_driver,soketi'
                ],
            ],
            'pusher_app_use_tls' => [
                'key' => 'pusher_app_use_tls',
                'data_type' => 2,     // bool
                'default' => true,
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required_if:broadcast_connection_driver,soketi'
                ],
            ],
            'pusher_app_encrypted' => [
                'key' => 'pusher_app_encrypted',
                'data_type' => 2,     // bool
                'default' => true,
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required_if:broadcast_connection_driver,soketi'
                ],
            ],
        ],
        'social-login' => [
            // Social Login Settings
            'allow_facebook_login'  => [
                'key'           => 'allow_facebook_login',
                'data_type'     => 2,     // boolean
                'default'       => false,
            ],
            'facebook_client_id'    => [
                'key'           => 'facebook_client_id',
                'data_type'     => 1,    // string
                'default'       => '',
                'hide_value'    => true,
                 'ignore_empty' => true,
                'validation_rules' => [
                    'required_if:allow_facebook_login,1'
                ],
            ],
            'facebook_client_secret' => [
                'key'           => 'facebook_client_secret',
                'data_type'     => 1,    // string
                'default'       => '',
                'hide_value'    => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required_if:allow_facebook_login,1'
                ],
            ],
            'allow_google_login' => [
                'key'           => 'allow_google_login',
                'data_type'     => 2,     // boolean
                'default'       => false
            ],
            'google_client_id'      => [
                'key'           => 'google_client_id',
                'data_type'     => 1,    // string
                'default'       => '',
                'hide_value'    => true,
                'validation_rules' => [
                    'required_if:allow_google_login,1'
                ],
            ],
            'google_client_secret'  => [
                'key'           => 'google_client_secret',
                'data_type'     => 1,    // string
                'default'       => '',
                'hide_value'    => true,
                'validation_rules' => [
                    'required_if:allow_google_login,1'
                ],
            ],
        ],
        'integrations' => [
            'microsoft_translator_api_key' => [
                'key' => 'microsoft_translator_api_key',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
            ],
            'microsoft_translator_api_region' => [
                'key' => 'microsoft_translator_api_region',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required_if:microsoft_translator_api_key,1',
                    'alpha_num'
               ],
            ],
            'recaptcha_site_key' => [
                'key' => 'recaptcha_site_key',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                     'required_if:enable_recaptcha,1'
                ],
            ],
            'recaptcha_secret_key' => [
                'key' => 'recaptcha_secret_key',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                     'required_if:enable_recaptcha,1'
                ],
            ],
            'enable_recaptcha' => [
                'key' => 'allow_recaptcha',
                'data_type' => 2,    // boolean
                'default' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    // 'required',
                ],
            ],
            'api_documentation_url' => [
                'key' => 'api_documentation_url',
                'data_type' => 1,     // string
                'default' => 'https://documenter.getpostman.com/view/17404097/2sA35D4hpx',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                    'url',
                ],
            ],
            'page_footer_code_all' => [
                'key' => 'page_footer_code_all',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => false, // it won't encrypt values
                'ignore_empty' => false, // it will allow blank entry
                'validation_rules' => [
                ],
            ],
            'page_footer_code_logged_user_only' => [
                'key' => 'page_footer_code_logged_user_only',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => false, // it won't encrypt values
                'ignore_empty' => false, // it will allow blank entry
                'validation_rules' => [
                ],
            ],
        ],
        'misc_settings' => [
            'current_home_page_view' => [
                'key' => 'current_home_page_view',
                'data_type' => 1,     // string
                'default' => 'outer-home-2',
                'hide_value' => false,
                'ignore_empty' => false,
                'validation_rules' => [
                    'required',
                ],
            ],
            'other_home_page_url' => [
                'key' => 'other_home_page_url',
                'data_type' => 1,     // string
                'default' => '',
                'hide_value' => false,
                'ignore_empty' => false,
                'validation_rules' => [
                    'nullable',
                    'url',
                ],
            ],
        ],
        'language-settings' => [
            'translation_languages' => [
                'key' => 'translation_languages',
                'data_type' => 4,    // string
                'default' => '',
            ],
        ],
        'email' => [
            'use_env_default_email_settings' => [
                'key' => 'use_env_default_email_settings',
                'data_type' => 2,    // boolean
                'placeholder' => '',
                'default' => true,
            ],
            'mail_driver' => [
                'key' => 'mail_driver',
                'data_type' => 1,    // integer
                'placeholder' => '',
                'default' => 'smtp',
            ],
            'mail_from_address' => [
                'key' => 'mail_from_address',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'mail_from_name' => [
                'key' => 'mail_from_name',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'smtp_mail_port' => [
                'key' => 'smtp_mail_port',
                'data_type' => 3,    // integer
                'placeholder' => '',
                'default' => null,
            ],
            'smtp_mail_host' => [
                'key' => 'smtp_mail_host',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'smtp_mail_username' => [
                'key' => 'smtp_mail_username',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'smtp_mail_encryption' => [
                'key' => 'smtp_mail_encryption',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'smtp_mail_password_or_apikey' => [
                'key' => 'smtp_mail_password_or_apikey',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'sparkpost_mail_password_or_apikey' => [
                'key' => 'sparkpost_mail_password_or_apikey',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'mailgun_mail_password_or_apikey' => [
                'key' => 'mailgun_mail_password_or_apikey',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'mailgun_domain' => [
                'key' => 'mailgun_domain',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
            'mailgun_endpoint' => [
                'key' => 'mailgun_endpoint',
                'data_type' => 1,    // string
                'placeholder' => '',
                'default' => '',
            ],
        ],
        'product_registration' => [
            'product_registration' => [
                'key' => 'product_registration',
                'data_type' => 4, // json
                'default' => [
                    'registration_id' => '',
                    'email' => '',
                    'registered_at' => '',
                    'licence' => '',
                    'signature' => '',
                ],
            ],
        ],
        'subscription_plans' => [
            'subscription_plans' => [
                'key' => 'subscription_plans',
                'data_type' => 4, // json
                'default' => [],
            ],
        ],
        'manual_whatsapp_onboarding' => [
            'enable_whatsapp_manual_signup' => [
                'key' => 'enable_whatsapp_manual_signup',
                'data_type' => 2,    // boolean
                'default' => true,
            ],
        ],
        'whatsapp_onboarding' => [
            'enable_embedded_signup' => [
                'key' => 'enable_embedded_signup',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'embedded_signup_app_id' => [
                'key' => 'embedded_signup_app_id',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'nullable',
                    'numeric',
                    'required_if:enable_embedded_signup,on',
                ],
            ],
            'embedded_signup_app_secret' => [
                'key' => 'embedded_signup_app_secret',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'nullable',
                    'alpha_num',
                    'required_if:enable_embedded_signup,on',
                ],
            ],
            'embedded_signup_config_id' => [
                'key' => 'embedded_signup_config_id',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'nullable',
                    'numeric',
                    'required_if:enable_embedded_signup,on',
                ],
            ],
        ],
        'internals' => [
            // non interface based settings info, INTERNAL USE ONLY
            'cron_setup_done_at' => [
                'key'           => 'cron_setup_done_at',
                'data_type'     => 1,    // string
                'placeholder'   => '',
                'default'       => false,
                'ignore_empty' => true
            ],
            'cron_setup_using_artisan_at' => [
                'key'           => 'cron_setup_using_artisan_at',
                'data_type'     => 1,    // string
                'placeholder'   => '',
                'default'       => false,
                'ignore_empty' => true
            ],
            'payment_gateway_info' => [
                'key'           => 'payment_gateway_info',
                'data_type'     => 4,    // json,
                'placeholder'   => '',
                'default'       => '',
                'ignore_empty' => true
            ],
        ]
    ],
];
