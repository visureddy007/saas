<?php
putenv('LC_ALL=en');

// default locale
$locale = 'en';
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (! function_exists('changeAppLocale')) {
    function changeAppLocale($localeId = null, $localeConfig = null)
    {
        // define constants for locale
        if (! defined('LOCALE_DIR')) {
            define('LOCALE_DIR', base_path('locale'));
        }
        if (! $localeConfig) {
            $localeConfig = config('locale');
        }
        // $availableLocale = json_decode(getConfigurationSettings('translation_languages'), true);

        $availableLocale = (getAppSettings('translation_languages'));

        if (__isEmpty($availableLocale)) {
            $availableLocale = [];
        }

        $availableLocale['en'] = [
            'id' => 'en',
            'name' => __tr('English'),
            'is_rtl' => false,
            'status' => true,
        ];

        $availableLocale[config('__tech.default_translation_language.id', 'en')] = configItem('default_translation_language');
        $userBrowserLocale = $locale = getAppSettings('default_language') ?: config('app.locale');

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) and function_exists('locale_accept_from_http')) {
            $userBrowserLocale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }

        // check if language is exist
        if ($userBrowserLocale and array_key_exists($userBrowserLocale, $availableLocale)) {
            $locale = $userBrowserLocale;
        }

        // check if locale is available
        if ($localeId and array_key_exists($localeId, $availableLocale)) {
            $locale = $localeId;
            // set current locale in session
            $_SESSION['CURRENT_LOCALE'] = $locale;
            // check if current locale is already set if yes use it
        } elseif (isset($_SESSION['CURRENT_LOCALE']) and $_SESSION['CURRENT_LOCALE']) {
            $locale = $_SESSION['CURRENT_LOCALE'];
        }

        // define constant for current locale
        $direction = 'ltr';
        if (
            isset($availableLocale[$locale])
            and $availableLocale[$locale]['is_rtl'] == true
        ) {
            $direction = 'rtl';
        }
        $domain = 'messages';
        putenv('LC_ALL='.$locale.'.utf8');
        T_setlocale(LC_ALL, $locale.'.utf8');
        T_bindtextdomain($domain, LOCALE_DIR);
        T_bind_textdomain_codeset($domain, 'UTF-8');
        T_textdomain($domain);
        \Illuminate\Support\Facades\View::share('CURRENT_LOCALE_DIRECTION', $direction);
        \App::setLocale(substr($locale, 0, 2));
        \Carbon\Carbon::setLocale($locale, 'UTF-8');
        // reinit configs for translations
        config([
            '__tech' => require config_path('__tech.php'),
            '__settings' => require config_path('__settings.php'),
            '__vendor-settings' => require config_path('__vendor-settings.php'),
            'lw-plans' => require config_path('lw-plans.php'),
        ]);
    }
}