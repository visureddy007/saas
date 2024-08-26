<?php

use Illuminate\Support\Str;

// dummy method to supress the errors
if (! function_exists('T_gettext')) {
    function T_gettext($string)
    {
        return $string;
    }
}

if (! function_exists('T_ngettext')) {
    function T_ngettext($string, $string2, $int)
    {
        return $string;
    }
}
if (! function_exists('__tr')) {
    /**
     * String translations for gettext
     */
    function __tr(?string $string, array $replaceValues = [], bool $escapeInputString = true): string
    {
        if ($string === null) {
            return '';
        }
        $originalString = $string;
        // skip to escape if it contains ' or " so the words like don't etc work well
        if (Str::contains($string, [
            "'",
            '"',
        ])) {
            $string = T_gettext($string);
        } else {
            $string = $escapeInputString ? e(T_gettext($string)) : T_gettext($string);
        }

        // Check if replaceValues exist
        if (! empty($replaceValues) and is_array($replaceValues)) {
            $string = strtr($string, $replaceValues);
        }
        $configLocale = config('app.locale');
        if (! in_array($configLocale, [
            'en',
            'en_US',
        ]) and extension_loaded('intl')) {
            // if numbers found in string change those also.
            $string = preg_replace_callback('!\d+!', function ($matches) use ($configLocale) {
                if (class_exists('NumberFormatter')) {
                    $numberFormatter = new NumberFormatter(
                        $configLocale ? $configLocale : 'en_US',
                        NumberFormatter::IGNORE
                    );

                    // to prevent removing leading zeros added this trick
                    return preg_replace_callback('!\d!', function ($subMatches) use ($numberFormatter) {
                        return $numberFormatter->format($subMatches[0]);
                    }, $matches[0]);
                }
            }, $string);
            unset($matches);
        }
        // check if there is blank string after processed
        // in such case we may need to use original string
        if (! $string) {
            // Check if replaceValues exist
            if (! empty($replaceValues) and is_array($replaceValues)) {
                $originalString = strtr($originalString, $replaceValues);
            }

            return $originalString;
        }

        return $string;
    }
}

if (! function_exists('__trn')) {
    /**
     * Translation for Plurals
     */
    function __trn(string $string, string $string2 = '', int $int = 1, array $replaceValues = []): string
    {
        $int = (int) $int;
        $string = e(T_ngettext($string, $string2, $int));
        // Check if replaceValues exist
        if (! empty($replaceValues) and is_array($replaceValues)) {
            $string = strtr($string, $replaceValues);
        }
        if (extension_loaded('intl')) {
            // if numbers found in string change those also.
            $string = preg_replace_callback('!\d+!', function ($matches) {
                if (class_exists('NumberFormatter')) {
                    $numberFormatter = new NumberFormatter(
                        app()->getLocale(),
                        NumberFormatter::IGNORE
                    );

                    return $numberFormatter->format($matches[0]);
                }
            }, $string);

            unset($matches);
        }

        return $string;
    }
}
