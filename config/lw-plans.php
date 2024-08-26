<?php

return [
    'free' => [ // only one plan, do not change this key
        'id' => 'free', // do not change this id
        'enabled' => true,
        'title' => 'Free',
        'trial_days' => 0, // not in use as free plan
        'features' => [
            'contacts' => [
                'description' => __tr('Contacts'),
                'limit' => 2, // 0 for none, -1 for unlimited
            ],
            'campaigns' => [
                'limit_duration' => 'monthly',
                'limit_duration_title' => __tr('Per Month'),
                'description' => __tr('Campaigns'),
                'limit' => 10, // 0 for none, -1 for unlimited
            ],
            'bot_replies' => [
                'description' => __tr('Bot Replies'),
                'limit' => 10, // 0 for none, -1 for unlimited
            ],
            'bot_flows' => [
                'description' => __tr('Bot Flows'),
                'limit' => 5, // 0 for none, -1 for unlimited
            ],
            'contact_custom_fields' => [
                'description' => __tr('Contact Custom Fields'),
                'limit' => 2, // 0 for none, -1 for unlimited
            ],
            'system_users' => [
                'description' => __tr('Team Members/Agents'),
                'limit' => 0, // 0 for none, -1 for unlimited
            ],
            'ai_chat_bot' => [
                'type' => 'switch', // on or off
                'description' => __tr('AI Chat Bot'),
                'limit' => 1, // 0 for none, 1 for enable
            ],
            'api_access' => [
                'type' => 'switch', // on or off
                'description' => __tr('API and Webhook Access'),
                'limit' => 1, // 0 for none, 1 for enable
            ],
        ],
    ],
    'paid' => [ // do not change this key
        'plan_1' => [
            'id' => 'plan_1',
            'enabled' => false,
            'popular' => true, // set plan as popular
            'title' => 'Standard',
            'trial_days' => 0,
            'features' => [
                'contacts' => [
                    'description' => __tr('Contacts'),
                    'limit' => 5, // 0 for none, -1 for unlimited
                ],
                'campaigns' => [
                    'limit_duration' => 'monthly',
                    'limit_duration_title' => __tr('Per Month'),
                    'description' => __tr('Campaigns'),
                    'limit' => 10, // 0 for none, -1 for unlimited
                ],
                'bot_replies' => [
                    'description' => __tr('Bot Replies'),
                    'limit' => 10, // 0 for none, -1 for unlimited
                ],
                'bot_flows' => [
                    'description' => __tr('Bot Flows'),
                    'limit' => 5, // 0 for none, -1 for unlimited
                ],
                'contact_custom_fields' => [
                    'description' => __tr('Contact Custom Fields'),
                    'limit' => 5, // 0 for none, -1 for unlimited
                ],
                'system_users' => [
                'description' => __tr('Team Members/Agents'),
                'limit' => 5, // 0 for none, -1 for unlimited
            ],
            'ai_chat_bot' => [
                'type' => 'switch', // on or off
                'description' => __tr('AI Chat Bot'),
                'limit' => 1, // 0 for none and 1 for enable
            ],
            'api_access' => [
                'type' => 'switch', // on or off
                'description' => __tr('API and Webhook Access'),
                'limit' => 1, // 0 for none, 1 for enable
            ],
            ],
            'charges' => [
                'monthly' => [
                    'title' => __tr('monthly'),
                    'enabled' => false,
                    'price_id' => '',
                    'charge' => 10,
                ],
                'yearly' => [
                    'title' => __tr('yearly'),
                    'enabled' => false,
                    'price_id' => '',
                    'charge' => 100,
                ],
            ],
        ],
        'plan_2' => [
            'id' => 'plan_2',
            'enabled' => false,
            'popular' => false, // set plan as popular
            'title' => 'Premium',
            'trial_days' => 0,
            'features' => [
                'contacts' => [
                    'description' => __tr('Contacts'),
                    'limit' => 15, // 0 for none, -1 for unlimited
                ],
                'campaigns' => [
                    'limit_duration' => 'monthly',
                    'limit_duration_title' => __tr('Per Month'),
                    'description' => __tr('Campaigns'),
                    'limit' => 10, // 0 for none, -1 for unlimited
                ],
                'bot_replies' => [
                    'description' => __tr('Bot Replies'),
                    'limit' => 10, // 0 for none, -1 for unlimited
                ],
                'bot_flows' => [
                    'description' => __tr('Bot Flows'),
                    'limit' => 5, // 0 for none, -1 for unlimited
                ],
                'contact_custom_fields' => [
                    'description' => __tr('Contact Custom Fields'),
                    'limit' => 10, // 0 for none, -1 for unlimited
                ],
                'system_users' => [
                'description' => __tr('Team Members/Agents'),
                'limit' => 10, // 0 for none, -1 for unlimited
            ],
            'ai_chat_bot' => [
                'type' => 'switch', // on or off
                'description' => __tr('AI Chat Bot'),
                'limit' => 1, // 0 for none, 1 for enable
            ],
            'api_access' => [
                'type' => 'switch', // on or off
                'description' => __tr('API and Webhook Access'),
                'limit' => 1, // 0 for none, 1 for enable
            ],
            ],
            'charges' => [
                'monthly' => [
                    'title' => __tr('monthly'),
                    'enabled' => false,
                    'price_id' => '',
                    'charge' => 20,
                ],
                'yearly' => [
                    'title' => __tr('yearly'),
                    'enabled' => false,
                    'price_id' => '',
                    'charge' => 199,
                ],
            ],
        ],
        'plan_3' => [
            'id' => 'plan_3',
            'enabled' => false,
            'popular' => false, // set plan as popular
            'title' => 'Ultimate',
            'trial_days' => 0,
            'features' => [
                'contacts' => [
                    'description' => __tr('Contacts'),
                    'limit' => -1, // 0 for none, -1 for unlimited
                ],
                'campaigns' => [
                    'limit_duration' => 'monthly',
                    'limit_duration_title' => __tr('Per Month'),
                    'description' => __tr('Campaigns'),
                    'limit' => -1, // 0 for none, -1 for unlimited
                ],
                'bot_replies' => [
                    'description' => __tr('Bot Replies'),
                    'limit' => -1, // 0 for none, -1 for unlimited
                ],
                'bot_flows' => [
                    'description' => __tr('Bot Flows'),
                    'limit' => -1, // 0 for none, -1 for unlimited
                ],
                'contact_custom_fields' => [
                    'description' => __tr('Contact Custom Fields'),
                    'limit' => -1, // 0 for none, -1 for unlimited
                ],
                'system_users' => [
                    'description' => __tr('Team Members/Agents'),
                    'limit' => -1, // 0 for none, -1 for unlimited
            ],
            'ai_chat_bot' => [
                'type' => 'switch', // on or off
                'description' => __tr('AI Chat Bot'),
                'limit' => 1, // 0 for none, 1 for enable
            ],
            'api_access' => [
                'type' => 'switch', // on or off
                'description' => __tr('API and Webhook Access'),
                'limit' => 1, // 0 for none, 1 for enable
            ],
            ],
            'charges' => [
                'monthly' => [
                    'title' => __tr('monthly'),
                    'enabled' => false,
                    'price_id' => '',
                    'charge' => 30,
                ],
                'yearly' => [
                    'title' => __tr('yearly'),
                    'enabled' => false,
                    'price_id' => '',
                    'charge' => 299,
                ],
            ],
        ],
    ],
];
