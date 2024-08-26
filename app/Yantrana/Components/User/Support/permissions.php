<?php

/**
 * Permissions
 *-----------------------------------------------------------------------------*/

return [
    'administrative' => [
        'title' => __tr('Administrative'),
        'description' => __tr('Allow/Deny permissions like Configuration, Subscription, Team Members etc'),
    ],
    'manage_contacts' => [
        'title' => __tr('Manage Contacts'),
        'description' => __tr('Allow/Deny access for Manage Contacts, Groups, Custom Contact Fields etc'),
    ],
    'manage_campaigns' => [
        'title' => __tr('Manage Campaigns'),
        'description' => __tr('Allow/Deny access like Creating, Executing and Scheduling Campaigns etc'),
    ],
    'messaging' => [
        'title' => __tr('Messaging'),
        'description' => __tr('Allow/Deny access like Chat, Sync Templates etc'),
    ],
    'manage_templates' => [
        'title' => __tr('Manage Templates'),
        'description' => __tr('Allow/Deny access like Creating, Editing and Deleting Templates etc'),
    ],
    'assigned_chats_only' => [
        'title' => __tr('Assigned Chat Only'),
        'description' => __tr('Restrict users to assigned chat only, unless they will have access to all chats'),
    ],
    'manage_bot_replies' => [
        'title' => __tr('Manage Bot Replies and Flows'),
        'description' => __tr('Allow/Deny access for Bot Replies and Flows'),
    ],
];