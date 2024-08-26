<?php

return [

    /* Configuration setting data-types id
    ------------------------------------------------------------------------- */
    'datatypes' => [
        'string' => 1,
        'bool' => 2,
        'int' => 3,
        'json' => 4,
        'float' => 6,
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
            ],
            'favicon_name' => [
                'key' => 'favicon_name',
                'data_type' => 1,    // string,
                'placeholder' => '',
                'default' => 'favicon.ico',
            ],
            'name' => [
                'key' => 'name',
                'data_type' => 1,    // string,
                'placeholder' => __tr('Your Business Name'),
                'default' => 'business name',
                'validation_rules' => [
                    'required',
                ],
            ],
            'vendor_slug' => [
                'key' => 'vendor_slug',
                'data_type' => 1,    // string,
                'placeholder' => 'name in the url',
                'default' => uniqid(),
            ],
            'contact_email' => [
                'key' => 'contact_email',
                'data_type' => 1,    // string
                'placeholder' => 'your-email-address@example.com',
                'default' => '',
                'validation_rules' => [
                    'required',
                ],
            ],
            'contact_phone' => [
                'key' => 'contact_phone',
                'data_type' => 1,    // string
                'placeholder' => __tr('your business phone number'),
                'default' => '',
                'validation_rules' => [
                    'required',
                ],
            ],
            'address' => [
                'key' => 'address',
                'data_type' => 1,    // string
                'default' => '',
                'validation_rules' => [
                    'required',
                ],
            ],
            'postal_code' => [
                'key' => 'postal_code',
                'data_type' => 1,    // string
                'default' => '',
                'validation_rules' => [
                    'required',
                ],
            ],
            'city' => [
                'key' => 'city',
                'data_type' => 1,    // string
                'default' => '',
                'validation_rules' => [
                    'required',
                ],
            ],
            'state' => [
                'key' => 'state',
                'data_type' => 1,    // string
                'default' => '',
                'validation_rules' => [
                    'required',
                ],
            ],
            'country' => [
                'key' => 'country',
                'data_type' => 3,    // int
                'default' => '',
                'validation_rules' => [
                    'required',
                ],
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
                'validation_rules' => [
                    'required',
                ],
            ],
        ],
        'flowise_ai_bot_setup' => [
            'enable_flowise_ai_bot' => [
                'key' => 'enable_flowise_ai_bot',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'flowise_url' => [
                'key' => 'flowise_url',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required_if:enable_flowise_ai_bot,on',
                    'nullable',
                    'url',
                ],
            ],
            'flowise_access_token' => [
                'key' => 'flowise_access_token',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                //    'alpha_dash',
                   'nullable',
                ],
            ],
            'flowise_failed_message' => [
                'key' => 'flowise_failed_message',
                'data_type' => 1,    // string
                'default' => 'Sorry but something went wrong while processing your request by our AI.',
                'hide_value' => false,
                'ignore_empty' => false,
                'validation_rules' => [
                //    'required',
                ],
            ],
            'default_enable_flowise_ai_bot_for_users' => [
                'key' => 'default_enable_flowise_ai_bot_for_users',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
        ],
        /**
         * WhatsApp Cloud API setup
         */
        'whatsapp_cloud_api_setup' => [
            'facebook_app_id' => [
                'key' => 'facebook_app_id',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                    'numeric',
                ],
            ],
            'facebook_app_secret' => [
                'key' => 'facebook_app_secret',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                    'alpha_num',
                ],
            ],
            'whatsapp_access_token' => [
                'key' => 'whatsapp_access_token',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                ],
            ],
            'whatsapp_business_account_id' => [
                'key' => 'whatsapp_business_account_id',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                    'numeric',
                ],
            ],
            'whatsapp_phone_numbers' => [
                'key' => 'whatsapp_phone_numbers',
                'data_type' => 4,    // json
                'default' => [],
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    // 'required',
                    // 'numeric',
                ],
            ],
            'current_phone_number_number' => [
                'key' => 'current_phone_number_number',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    // 'required',
                    // 'numeric',
                    // 'doesnt_start_with:+',
                ],
            ],
            'current_phone_number_id' => [
                'key' => 'current_phone_number_id',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                    // 'required',
                    // 'numeric',
                ],
            ],
            'webhook_verified_at' => [
                'key' => 'webhook_verified_at',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                ],
            ],
            'webhook_messages_field_verified_at' => [
                'key' => 'webhook_messages_field_verified_at',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                ],
            ],
            // internal uses
            'whatsapp_onboarding_raw_data' => [
                'key' => 'whatsapp_onboarding_raw_data',
                'data_type' => 4,    // json
                'default' => '',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                ],
            ],
            // internal uses
            'whatsapp_phone_numbers_data' => [
                'key' => 'whatsapp_phone_numbers_data',
                'data_type' => 4,    // json
                'default' => '',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                ],
            ],
            'whatsapp_token_info_data' => [
                'key' => 'whatsapp_token_info_data',
                'data_type' => 4,    // json
                'default' => '',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                ],
            ],
            'embedded_setup_done_at' => [
                'key'           => 'embedded_setup_done_at',
                'data_type'     => 1,    // string,
                'placeholder'   => '',
                'default'       => false,
                'ignore_empty' => true,
            ],
            'test_recipient_contact' => [
                'key' => 'test_recipient_contact',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => false,
                'ignore_empty' => true,
                'validation_rules' => [
                    'required',
                    'numeric',
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
        'internals' => [
            // non interface based settings info, INTERNAL USE ONLY
            'whatsapp_access_token_expired' => [
                'key'           => 'whatsapp_access_token_expired',
                'data_type'     => 2,    // bool,
                'placeholder'   => '',
                'default'       => false,
                'ignore_empty' => false
            ],
            'whatsapp_health_status_data' => [
                'key'           => 'whatsapp_health_status_data',
                'data_type'     => 4,    // json,
                'placeholder'   => '',
                'default'       => '',
                'ignore_empty' => true
            ],
            'is_disabled_message_sound_notification' => [
                'key' => 'is_disabled_message_sound_notification',
                'data_type' => 2,    // bool
                'placeholder' => '',
                'default' => '',
            ],
            'vendor_api_access_token' => [
                'key' => 'vendor_api_access_token',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => true,
                'ignore_empty' => true,
                'validation_rules' => [
                ],
            ],
            // webhook
            'enable_vendor_webhook' => [
                'key' => 'enable_vendor_webhook',
                'data_type' => 2,    // boolean
                'default' => false,
            ],
            'vendor_webhook_endpoint' => [
                'key' => 'vendor_webhook_endpoint',
                'data_type' => 1,    // string
                'default' => '',
                'hide_value' => false,
                'ignore_empty' => false,
                'validation_rules' => [
                    'required_if:enable_vendor_webhook,on',
                    'nullable',
                    'url'
                ],
            ],
        ],
    ],
];
