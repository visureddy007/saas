<?php

changeAppLocale();
// default time zone
$timeZone = 'UTC';

date_default_timezone_set($timeZone);

config([
    'filesystems.public-media-storage.url' => asset(''),
    'app.name' => getAppSettings('name'),
]);
// Note: User is not available in login state here so we are unable to determine vendor
if(getAppSettings('broadcast_connection_driver')) {
    if(getAppSettings('pusher_app_id')) {
        // update pusher settings
        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.app_id' => getAppSettings('pusher_app_id'),
            'broadcasting.connections.pusher.key' => getAppSettings('pusher_app_key'),
            'broadcasting.connections.pusher.secret' => getAppSettings('pusher_app_secret'),
            'broadcasting.connections.pusher.options.cluster' => getAppSettings('pusher_app_cluster'),
        ]);

        if((getAppSettings('broadcast_connection_driver') == 'soketi')) {
            // update soketi settings
            config([
                'broadcasting.connections.pusher.options.host' => getAppSettings('pusher_app_host'),
                'broadcasting.connections.pusher.options.port' => getAppSettings('pusher_app_port'),
                'broadcasting.connections.pusher.options.scheme' => getAppSettings('pusher_app_scheme'),
                'broadcasting.connections.pusher.options.useTLS' => getAppSettings('pusher_app_use_tls'),
                'broadcasting.connections.pusher.options.encrypted' => getAppSettings('pusher_app_encrypted'),
            ]);
        }
    }
}

if (getAppSettings('enable_stripe')) {
    if (getAppSettings('use_test_stripe')) {
        config([
            'cashier.key' => getAppSettings('stripe_testing_publishable_key'),
            'cashier.secret' => getAppSettings('stripe_testing_secret_key'),
            'cashier.webhook.secret' => getAppSettings('stripe_testing_webhook_secret'),
            'cashier.currency_locale' => str_replace('_', '-', app()->getLocale()),
        ]);
    } else {
        config([
            'cashier.key' => getAppSettings('stripe_live_publishable_key'),
            'cashier.secret' => getAppSettings('stripe_live_secret_key'),
            'cashier.webhook.secret' => getAppSettings('stripe_live_webhook_secret'),
            'cashier.currency_locale' => str_replace('_', '-', app()->getLocale()),
        ]);
    }
}
if (getAppSettings('use_env_default_email_settings') == false) {
    config([
        // Mail driver
        'mail.driver' => getAppSettings('mail_driver'),
        'mail.transport' => getAppSettings('mail_driver'),

        // Mail Setting for SMTP and Mandrill
        'mail.port' => getAppSettings('smtp_mail_port'),
        'mail.host' => getAppSettings('smtp_mail_host'),
        'mail.username' => getAppSettings('smtp_mail_username'),
        'mail.encryption' => getAppSettings('smtp_mail_encryption'),
        'mail.password' => getAppSettings('smtp_mail_password_or_apikey'),
        'mail.from.address' => getAppSettings('mail_from_address'),
        'mail.from.name' => getAppSettings('mail_from_name'),
        // Mail Setting for Sparkpost
        'services.sparkpost.secret' => getAppSettings('sparkpost_mail_password_or_apikey'),
        // Mail Setting for Mailgun
        'services.mailgun.domain' => getAppSettings('mailgun_domain'),
        'services.mailgun.secret' => getAppSettings('mailgun_mail_password_or_apikey'),
        'services.mailgun.endpoint' => getAppSettings('mailgun_endpoint'),
        '__tech.mail_from' => [
            getAppSettings('mail_from_address'), getAppSettings('mail_from_name'),
        ],
    ]);
}
