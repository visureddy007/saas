<?php

/**
 * Core Helper - 1.19.50 - 07 AUG 2024
 *
 * Common helper functions for Laravel applications
 *
 *
 * Dependencies:
 *
 * Laravel     5.0 +     - http://laravel.com
 *-------------------------------------------------------- */

/**
 * State route for StateViaRoute function
 *
 *-------------------------------------------------------- */
Route::get('/state-via-route/{stateRouteInfo}', [
    'as' => '__laraware.state_via_route',
    'uses' => 'App\Yantrana\__Laraware\Support\CommonSupport@stateViaRoute',
]);

/**
 * State route for StateViaRoute function
 *
 *-------------------------------------------------------- */
Route::get('/redirect-via-post/{redirectPostData}', [
    'as' => '__laraware.redirect_via_post',
    'uses' => 'App\Yantrana\__Laraware\Support\CommonSupport@redirectViaPost',
]);

/**
 * State route for StateViaRoute function
 *
 *-------------------------------------------------------- */
Route::get('/post-event-streamed-request', function () {
    return 'done';
})->middleware('web')->name('__laraware.post_event_streamed_request');

/**
 * Enabling Debug modes for the specific ips
 *
 * @since 1.4.3 - 19 SEP 2018
 *
 * @updated 1.9.30 - 03 NOV 2022
 *-------------------------------------------------------- */
if (config('app.debug') == false) {
    if ($debugIps = config('laraware.app_debug_ips', false)) {
        if ($debugIps) {
            $debugIps = array_map('trim', explode(',', $debugIps));
            if (in_array(request()->getClientIp(), $debugIps)) {
                config([
                    'app.debug' => true,
                    // 'app.env' => 'local',
                ]);
                unset($debugIps);
            }
        }
    }
}

if (! function_exists('redirectViaPost')) {
    /**
     * Redirect using post
     *
     * @param  string routeData url or route name
     * @param  array postData data to post

     *-------------------------------------------------------- */
    function redirectViaPost($routeData, $postData = [], $tempRedirectData = [])
    {
        if (is_string($routeData) === false) {
            throw new Exception('route id should be string');
        }

        if (is_array($postData) === false) {
            throw new Exception('post data should be array');
        }

        if (starts_with($routeData, ['http://', 'https://'])) {
            $redirectRoute = $routeData;
        } else {
            $redirectRoute = route($routeData);
        }

        $postFieldString = '';

        foreach ($postData as $key => $value) {
            if (is_numeric($value) or is_string($value)) {
                $postFieldString .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
            } else {
                throw new Exception('value should be numeric or string');
            }
        }

        $tempRedirectData = json_encode($tempRedirectData);

        return <<<EOL
<!DOCTYPE html>
<html>
<head>
    <title>Redirecting ...</title>
</head>
    <body>
        Redirecting... please wait
        <form id="redirectViaPostFormElement" action="$redirectRoute" method="post">
        $postFieldString;
        </form>
        <script type="text/javascript">
            var tempRedirectData = `$tempRedirectData`;
            if(tempRedirectData) {
                window.localStorage.setItem('temp_redirect_data', tempRedirectData);
            }
            function redirectPostForm() {
                document.getElementById('redirectViaPostFormElement').submit();
            }
            window.onload = redirectPostForm;
        </script>
    </body>
</html>
EOL;
    }
}

if (! function_exists('stateViaRoute')) {
    /**
     * State via route function
     * Mostly get used for AngularJS state
     *
     * @param  routeData
     * @param  stateData

     *-------------------------------------------------------- */
    function stateViaRoute($routeData, $stateData)
    {
        $routeId = $routeData;
        $routeParams = [];

        if (is_array($routeData) and isset($routeData[0]) and is_string($routeData[0])) {
            $routeId = $routeData[0];

            if (isset($routeData[1])) {
                if (is_array($routeData[1])) {
                    $routeParams = $routeData[1];
                } else {
                    $routeParams[] = $routeData[1];
                }
            }
        }

        $stateId = $stateData;
        $stateParams = [];

        if (is_array($stateData) and isset($stateData[0]) and is_string($stateData[0])) {
            $stateId = $stateData[0];

            if (isset($stateData[1]) and is_array($stateData[1])) {
                $stateParams = array_only($stateData[1], array_filter(array_keys($stateData[1]), 'is_string'));
            }
        }

        if (is_string($routeId) === false) {
            throw new Exception('route id should be string');
        }

        if (is_string($stateId) === false) {
            throw new Exception('route id should be string');
        }

        $stateViaRouteInfo = [
            'routeId' => $routeId,
            'routeParams' => $routeParams,
            'stateName' => $stateId,
            'stateParams' => $stateParams,
        ];

        return route('__laraware.state_via_route', base64_encode(json_encode($stateViaRouteInfo)));
    }
}

if (! function_exists('__dd')) {
    /**
     * Debugging function for debugging javascript side.
     * Alias of Laravel dd function but works well with  Ajax request by showing
     * it in browser console as well with file path and line no.
     *
     * @param  N numbers of params can be sent
     *
     * @updated - 1.12.34 - 30 SEP 2023
     *-------------------------------------------------------- */
    function __dd()
    {
        // fix for event based response etc
        if (! headers_sent() and (Request::ajax() === true)) {
            header('Content-Type: application/json; charset=utf-8');
        }
        if (config('app.debug', false) == false) {
            throw new Exception('Something went wrong!!');
        }

        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__dd() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();
        // Editors Supported: "phpstorm", "vscode", "vscode-insiders","sublime", "atom"
        // vscode as default editor if not set from env
        if (! env('IGNITION_EDITOR')) {
            config(['ignition.editor' => 'vscode']);
        }
        $editor = config('ignition.editor', 'vscode');

        if (isset($backtrace[0])) {
            // if the using Homestead
            $backtrace[0]['file'] = $editor.'://file/'.str_replace(env('IGNITION_REMOTE_SITES_PATH', '/home/vagrant/code'), env('IGNITION_LOCAL_SITES_PATH', '/Volumes/DATA-HD/__HTDOCS'), $backtrace[0]['file']).':'.$backtrace[0]['line'];
            $args['debug_backtrace'] = $backtrace[0]['file'].'#';
        }

        if ((Request::ajax() === false) or (isExternalApiRequest())) {
            if(!isExternalApiRequest()) {
                echo '<a style="background: lightcoral;font-family: monospace;padding: 4px 8px;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;" href="'.$backtrace[0]['file'].'">Open in Editor ('.$editor.')</a>';
                echo <<<END
                <button onclick="var compacted = document.querySelectorAll('.sf-dump-compact');for (var i = 0; i < compacted.length; i++) {compacted[i].className = 'sf-dump-expanded';};var compacted = document.querySelectorAll('.sf-dump-str-toggle');for (var i = 0; i < compacted.length; i++) {compacted[i].click();}" style='margin-left:8px;border:none;background: darkgray;font-family: monospace;padding: 4px 8px;cursor:pointer;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;'>Expand All</button>
                END;
            }
            // call for dd
            call_user_func_array('dd', $args);
            exit();
        }

        exit(json_encode(array_merge(__response([], 23), [ // debug reaction
            '__dd' => '__dd',
            'data' => array_map(function ($argument) {
                return print_r($argument, true);
            }, $args),
        ])));
    }
}

if (! function_exists('__pr')) {
    /**
     * Debugging function for debugging javascript as well as PHP side, work as likely print_r but accepts unlimited parameters
     * Works well with  Ajax request by showing
     * it in browser console as well with file path and line no.
     *
     * @param  N numbers of params can be sent
     * @return void
     *-------------------------------------------------------- */
    function __pr()
    {
        if (config('app.debug', false) == false) {
            return false;
        }

        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__pr() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();

        // vscode as default editor if not set from env
        if (! env('IGNITION_EDITOR')) {
            config(['ignition.editor' => 'vscode']);
        }
        $editor = config('ignition.editor', 'vscode');

        if (isset($backtrace[0])) {
            // if the using Homestead
            $backtrace[0]['file'] = $editor.'://file/'.str_replace(env('IGNITION_REMOTE_SITES_PATH', '/home/vagrant/code'), env('IGNITION_LOCAL_SITES_PATH', '/Volumes/DATA-HD/__HTDOCS'), $backtrace[0]['file']).':'.$backtrace[0]['line'];
            $args['debug_backtrace'] = $backtrace[0]['file'].'#';
        }

        if (Request::ajax() === false) {
            echo '<a style="background: lightcoral;font-family: monospace;padding: 4px 8px;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;" href="'.$backtrace[0]['file'].'">Open in Editor ('.$editor.')</a>';
            echo <<<END
            <button onclick="var compacted = document.querySelectorAll('.sf-dump-compact');for (var i = 0; i < compacted.length; i++) {compacted[i].className = 'sf-dump-expanded';};var compacted = document.querySelectorAll('.sf-dump-str-toggle');for (var i = 0; i < compacted.length; i++) {compacted[i].click();}" style='margin-left:8px;border:none;background: darkgray;font-family: monospace;padding: 4px 8px;cursor:pointer;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;'>Expand All</button>
            END;

            if (class_exists('\Illuminate\Support\Debug\Dumper')) {
                return array_map(function ($argument) {
                    (new \Illuminate\Support\Debug\Dumper())->dump($argument, false);
                }, $args);
            } elseif (function_exists('dump')) {
                return dump($args);
            } else {
                return array_map(function ($argument) {
                    print_r($argument, false);
                }, $args);
            }
        }

        return config([
            'app.__pr.'.count(config('app.__pr', [])) => array_map(function ($argument) {
                return print_r($argument, true);
            }, $args),
        ]);
    }
}

if (! function_exists('__logDebug')) {
    /**
     * Log helper
     * Writes data in Laravel log file with File Path and Line Number
     *
     * @param  N numbers of params can be sent
     * @return void
     *
     * @since - 1.5.3 - 20 SEP 2018
     *
     * @version 1.9.31 - 14 APR 2023
     *-------------------------------------------------------- */
    function __logDebug()
    {
        if (config('app.debug', false) == false) {
            return false;
        }
        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__logDebug() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();
        if (isset($backtrace[0])) {
            $args['debug_backtrace'] = ' logged @ file --------------->  '.$backtrace[0]['file'] = str_replace(env('IGNITION_REMOTE_SITES_PATH', '/home/vagrant/code'), env('IGNITION_LOCAL_SITES_PATH', '/Volumes/DATA-HD/__HTDOCS'), $backtrace[0]['file']).':'.$backtrace[0]['line'];
        }

        return array_map(function ($argument) {
            Log::debug(print_r($argument, true));
        }, $args);

        return Log::debug($args);
    }
}

if (! function_exists('__clog')) {
    /**
     * Debugging function for debugging javascript
     * Prints data in browser console with file path and line number
     *
     * @param  N numbers of params can be sent
     * @return void
     *-------------------------------------------------------- */
    function __clog()
    {
        if (config('app.debug', false) == false) {
            return false;
        }

        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__clog() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();

        if (isset($backtrace[0])) {
            if (! env('IGNITION_EDITOR')) {
                config(['ignition.editor' => 'vscode']);
            }
            $editor = config('ignition.editor', 'vscode');
            // if the using Homestead
            $backtrace[0]['file'] = str_replace(env('IGNITION_REMOTE_SITES_PATH', '/home/vagrant/code'), env('IGNITION_LOCAL_SITES_PATH', '/Volumes/DATA-HD/__HTDOCS'), $backtrace[0]['file']);
            $args['debug_backtrace'] = $editor.'://file/'.$backtrace[0]['file'].':'.$backtrace[0]['line'].'#';
        }

        return config([
            'app.__clog.'.count(config('app.__clog', [])) => array_map(function ($argument) {
                return print_r($argument, true);
            }, $args),
        ]);
    }
}

if (! function_exists('__nestedKeyValues')) {
    /**
     * Utility function to create array of nested array items strings (concatenate parent key in to child key) & assign values to it.
     *
     * @param  $inputArray  raw nested array
     * @param  $requestedJoiner  joiner or word for string concat
     * @param  $prepend  prepend string
     * @param  $allStages  if you want to create an array item for every stage
     * @return array
     *-------------------------------------------------------- */
    function __nestedKeyValues(array $inputArray, $requestedJoiner = '.', $prepend = null, $allStages = false)
    {
        $formattedArray = [];

        foreach ($inputArray as $key => $value) {
            $joiner = ($prepend == null) ? '' : $requestedJoiner;

            // if array run this again to grab the child items to process
            if (is_array($value)) {
                if ($allStages === true) {
                    array_push($formattedArray, $prepend);
                }

                $formattedArray = array_merge($formattedArray, __nestedKeyValues($value, $requestedJoiner, $prepend.$joiner.$key, $allStages));
            } else {
                // if key is not string push item in to array with required
                if (is_string($key) === false) {
                    if (is_string($value) === true) {
                        array_push($formattedArray, $prepend.$joiner.$value);
                    } else {
                        array_push($formattedArray, $value);
                    }
                } else {
                    // if want to have specific key
                    if (is_string($value) and substr($value, 0, 4) === 'key@') {
                        $formattedArray[substr($value, 4)] = $prepend.$joiner.$key;
                    } else {
                        $formattedArray[$prepend.$joiner.$key] = $value;
                    }
                }
            }
        }

        unset($prepend, $joiner, $requestedJoiner, $prepend, $allStages, $inputArray);

        return $formattedArray;
    }
}

if (! function_exists('__secureApiResponse')) {
    /**
     * Create JSON object for all HTTP request with Masked/Encrypted data
     *
     * @param  array  $data
     * @param  int|array  $reactionCode
     * @param  int  $httpCode  http response code @since 1.8.25 - 07 MAY 2021
     *-------------------------------------------------------- */
    function __secureApiResponse($data, $reactionCode = 1, $httpCode = null)
    {
        $data['__secureOutput'] = true;

        return __apiResponse($data, $reactionCode, $httpCode);
    }
}
// non encrypted
if (! function_exists('__apiResponse')) {
    /**
     * Prepare JSON Response
     *
     * @param  array  $data
     * @param  int|array  $reactionCode
     * @param  int  $httpCode  http response code @since 1.8.23 - 22 APR 2021
     * @return response
     */
    function __apiResponse($data, $reactionCode = 1, $httpCode = null)
    {
        if (($httpCode === null) and isset($reactionCode['http_code']) and $reactionCode['http_code']) {
            $httpCode = $reactionCode['http_code'];
        }

        if ($reactionCode === 21 and isset($data['redirect_to'])) {
            // if not ajax redirect from here
            if (! request()->ajax()) {
                if ($httpCode and is_int($httpCode)) {
                    return redirect($data['redirect_to'], $httpCode);
                } else {
                    return redirect($data['redirect_to']);
                }
            }
        }

        if (
            isset($data['__useNativeJsonEncode'])
            and $data['__useNativeJsonEncode'] === true
        ) {
            if ($httpCode and is_int($httpCode)) {
                http_response_code($httpCode);
            }

            return json_encode(__response($data, $reactionCode));
        }
        // ask to encrypt data to secure output
        if (
            isset($data['__secureOutput'])
            and $data['__secureOutput'] === true and ! config('app.debug')
        ) {
            array_pull($data, '__secureOutput');

            $data = [
                '__maskedData' => YesSecurity::encryptLongRSA(
                    __response($data, $reactionCode)
                ),
            ];

            unset($encryptedString, $reactionCode, $jsonStringsCollection);
        } else {
            $data = __response($data, $reactionCode);
        }

        if ($httpCode and is_int($httpCode)) {
            return Response::json($data, $httpCode);
        }

        return Response::json($data);
    }
}

/**
 * Echo JSON API response.
 *
 * @param  array  $data
 * @return JSON Object.
 *-------------------------------------------------------- */
if (! function_exists('__response')) {
    function __response($data, $reactionCode = 1)
    {
        // update client models as per response
        $updateClientModels = config('__update_client_models', []);
        $clientModels = [];
        if (! empty($updateClientModels)) {
            $clientModels = $updateClientModels;
            config([
                '__update_client_models' => [],
            ]);
            unset($updateClientModels);
        }

        if (Session::has('additional')) {
            $data['additional'] = Session::get('additional');
        }

        if (config('app.additional')) {
            $data['additional'] = config('app.additional');
        }

        $responseData = [
            //  'data' => $data,
            'response_token' => (int) Request::get('fresh'),
            'reaction' => $reactionCode,
            'incident' => isset($data['incident']) ? $data['incident'] : null,
            // update client models accordingly
            'client_models' => $clientModels,
        ];

        if (array_has($data, 'dataTableResponse')) {
            $responseData = array_merge($responseData, $data);
        } else {
            $responseData['data'] = $data;
        }

        if (Session::has('additional')) {
            $responseData['additional'] = Session::get('additional');
        }

        if (config('app.additional')) {
            $responseData['additional'] = config('app.additional');
            config(['app.additional' => null]);
        }

        // __pr() to print in console
        if (config('app.debug', false) == true) {
            $prSessItemName = '__pr';
            if (config('app.'.$prSessItemName)) {
                $responseData['__dd'] = true;
                // set for response
                $responseData[$prSessItemName] = config('app.'.$prSessItemName, []);
                config(['app.'.$prSessItemName => []]);
            }

            $clogSessItemName = '__clog';

            if (config('app.'.$clogSessItemName)) {
                $responseData['__dd'] = true;
                // set for response
                $responseData[$clogSessItemName] = config('app.'.$clogSessItemName, []);
                //reset the __clog items in config
                config(['app.'.$clogSessItemName => []]);
            }

            // email view debugging
            if (config('laraware.mail_view_debug', false) == true) {
                $testEmailViewSessName = '__emailDebugView';
                if (config('app.'.$testEmailViewSessName)) {
                    $responseData[$testEmailViewSessName] = config('app.'.$testEmailViewSessName, []);
                    //reset the testEmailViewSessName items in config
                    config(['app.'.$testEmailViewSessName => []]);
                }
            }
        }

        return $responseData;
    }
}

if (! function_exists('__') and ! config('__tech.gettext_fallback')) {
    /**
     * Customized GetText string
     *
     * @param  string  $string
     * @param  array  $replaceValues
     * @return string.
     *-------------------------------------------------------- */
    function __($string, $replaceValues = [])
    {
        if (function_exists('gettext') and getenv('LC_ALL') !== false) {
            $string = gettext($string);
        }

        // Check if replaceValues exist
        if (! empty($replaceValues) and is_array($replaceValues)) {
            $string = strtr($string, $replaceValues);
        }

        return $string;
    }
}

if (! function_exists('__yesset')) {
    /**
     * Generating public js/css links
     *
     * @version 1.9.29 - 11 MAY 2022 - Added prevent_exception option to suppress Exception is item not found
     *
     * @last-updated - 1.9.32 - 25 AUG 2023
     *
     * @param  string|array  $file  - file path from public path
     * @param  bool  $generateTag  - if you want to generate script/link tags
     * @return string
     *-------------------------------------------------------- */
    function __yesset($file, $generateTag = false, $options = [])
    {
        $options = array_merge([
            'random' => false,
            'prevent_exception' => false,
            'multiple_extensions' => false,
        ], $options);

        $filesString = '';
        $files = [];

        // if file is not array add it to array
        if (is_array($file) === false) {
            $files = [$file];
        } else {
            $files = $file;
        }

        foreach ($files as $keyFile) {
            $keyFile = strip_tags(trim($keyFile));

            // find actual files on the system based on the file path/name
            if ($options['multiple_extensions']) {
                $globFiles = glob($keyFile, GLOB_BRACE);
            } else {
                $globFiles = glob($keyFile);
            }

            $fileHash = null;
            $fileExt = null;

            if (empty($globFiles)) {
                // if debug mode on throw an exception
                if ((config('app.debug', false) === true) and ! ($options['prevent_exception'])) {
                    throw new Exception('Yesset file not found - '.$keyFile.'
                        Check * in file name.');
                } else {
                    // if not just create file name;
                    $getFileName = $keyFile;
                    // generate url based on file name & path
                    $fileString = asset($getFileName);
                }
            } else {
                // we need to get first item out of it.
                $getFileName = $globFiles[0];
                // if randomly any one if required
                if ($options['random'] === true) {
                    $getFileName = $globFiles[rand(0, count($globFiles) - 1)];
                }

                $fileinfo = pathinfo($getFileName);
                $fileExt = $fileinfo['extension'];
                // generate url based on file name & path
                // also append file hash to know the file has been changed.
                $fileHash = sha1_file($getFileName, false);
                $fileString = asset($getFileName).'?sign='.$fileHash;
            }

            // generate tags based on file extension
            // if file is array or generateTag is true
            if ((is_array($file) === true)
                or ($generateTag === true)
            ) {
                // get last 3 character from file name mostly file extension
                $jsItemTOMatch = 'js';
                if (! $fileExt) {
                    $fileExt = strtolower(substr($getFileName, -3));
                    $jsItemTOMatch = '.js';
                }

                switch ($fileExt) {
                    // script tag generation for JS file
                    case $jsItemTOMatch:
                        $filesString .= '<script type="text/javascript" src="'.$fileString.'"></script>'.PHP_EOL;
                        break;
                        // link tag generation for CSS file
                    case 'css':
                        $filesString .= '<link rel="stylesheet" type="text/css" href="'.$fileString.'"/>'.PHP_EOL;
                        break;

                    default:
                        $filesString .= $fileString;
                }

                continue;
            }
            // if its string just return it.
            $filesString = $fileString;
        }

        unset($files, $file, $generateTag);

        return $filesString;
    }
}

if (! function_exists('__secureProcessResponse')) {
    /**
     * Process response & send API response
     *
     * @param  int  $engineReaction  - Engine reaction
     * @param  array  $responses  - Response Messages as per reaction code
     * @param  array  $data  - Additional Data for success
     * @param  int  $httpCode  - @since 1.8.23 - 22 APR 2021
     * @return array
     *---------------------------------------------------------------- */
    function __secureProcessResponse(
        $engineReaction,
        $messageResponses = [],
        $data = [],
        $appendEngineData = false,
        $httpCode = null
    ) {
        $data['__secureOutput'] = true;

        return __processResponse(
            $engineReaction,
            $messageResponses,
            $data,
            $appendEngineData,
            $httpCode
        );
    }
}

if (! function_exists('__processResponse')) {
    /**
     * Process the response to send
     *
     * @param  int|array  $engineReaction
     * @param  array  $messageResponses
     * @param  array  $data
     * @param  bool  $appendEngineData
     * @param  int  $httpCode  - @since 1.8.23 - 22 APR 2021
     *
     * @last-modified - 1.15.40 - 07 MAR 2024
     *
     * @return response
     */
    function __processResponse(
        $engineReaction,
        $messageResponses = [],
        $data = [],
        $appendEngineData = false,
        $httpCode = null
    ) {
        if (__isValidReactionCode($engineReaction) === true) {
            // set the message if available in responses
            if(!isset($data['message'])) {
                $data['message'] = array_get($messageResponses, $engineReaction, '');
            }
            return __apiResponse($data, $engineReaction, $httpCode);
        }

        if (($httpCode === null) and isset($engineReaction['http_code']) and $engineReaction['http_code']) {
            $httpCode = $engineReaction['http_code'];
        }
        if ((isset($engineReaction['reaction_code'])) === false
            and isset($engineReaction['data']) === false
            and isset($engineReaction['message']) === false
        ) {
            throw new Exception('__processResponse:: Invalid Engine Reaction');
        }

        $reactionCode = $engineReaction['reaction_code'];
        $reactionMessage = $engineReaction['message'];

        // Use message if sent from EngineReaction
        if (__isEmpty($reactionMessage) === false) {
            $data['message'] = $reactionMessage;
            // else use process response messages
        } elseif ($messageResponses and array_key_exists($reactionCode, $messageResponses)) {
            $data['message'] = $messageResponses[$reactionCode];
        }

        $dataFromReaction = isset($engineReaction['data']) ? $engineReaction['data'] : [];

        if ($data === true or $appendEngineData === true) {
            if (is_array($data) === false or empty($data) === true) {
                $data = [];
            }

            if (__isEmpty($dataFromReaction) === false) {
                if (
                    is_array($dataFromReaction)
                    or is_object($dataFromReaction)
                ) {
                    $data = array_merge($data, (array) $dataFromReaction);
                }
            }
        }

        $data['incident'] = isset($dataFromReaction['incident']) ? $dataFromReaction['incident'] : null;

        return __apiResponse($data, $reactionCode, $httpCode);
    }
}

if (! function_exists('__ifIsset')) {
    /**
     * Check isset & __isEmpty & return the result based on values sent
     *
     * @param  mixed  $data  - Mixed data - Note: Should no used direct function etc
     * @param  mixed  $ifSetValue  - Value if result is true
     * @param  mixed  $ifNotSetValue  - Value if result is false
     * @return mixed
     *---------------------------------------------------------------- */
    function __ifIsset(&$data, $ifSetValue = '', $ifNotSetValue = '')
    {
        // check if value isset & not empty
        if ((isset($data) === true) and (__isEmpty($data) === false)) {
            if (! is_string($ifSetValue) and is_callable($ifSetValue) === true) {
                return call_user_func($ifSetValue, $data);
            } elseif ($ifSetValue === true) {
                return $data;
            } elseif ($ifSetValue !== '') {
                return $ifSetValue;
            }

            return true;
        } else {
            if (! is_string($ifNotSetValue) and is_callable($ifNotSetValue) === true) {
                return call_user_func($ifNotSetValue);
            } elseif ($ifNotSetValue !== '') {
                return $ifNotSetValue;
            }

            return false;
        }
    }
}

if (! function_exists('__isEmpty')) {
    /**
     * Customized isEmpty
     *
     * @param  mixed  $data  - Mixed data
     * @return array
     *---------------------------------------------------------------- */
    function __isEmpty($data)
    {
        if (empty($data) === false) {
            if (($data instanceof Illuminate\Database\Eloquent\Collection
                    or $data instanceof Illuminate\Pagination\Paginator
                    or $data instanceof Illuminate\Pagination\LengthAwarePaginator
                    or $data instanceof Illuminate\Support\Collection)
                and ($data->count() <= 0)
            ) {
                return true;
            } elseif (is_object($data)) {
                $data = (array) $data;

                return empty($data);
            }

            return false;
        }

        return true;
    }
}

if (! function_exists('__isValidReactionCode')) {
    /**
     * Customized isEmpty
     *
     * @param  int  $reactionCode  - Reaction Code
     * @return bool
     *---------------------------------------------------------------- */
    function __isValidReactionCode($reactionCode)
    {
        if (
            is_int($reactionCode) === true
            and array_key_exists(
                $reactionCode,
                config('__tech.reaction_codes')
            ) === true
        ) {
            return true;
        }

        return false;
    }
}

if (! function_exists('__reIndexArray')) {
    /**
     * Re Indexing using array value based on key
     *
     * @param  string  $valueKey
     * @param closure function $closure
     *
     * @since - 29 JUN 2017
     *
     * @example uses
                ]

     * @return array
     *-------------------------------------------------------- */
    function __reIndexArray(array $array, $valueKey, $closure = null)
    {
        $newArray = [];
        if (! empty($array)) {
            foreach ($array as $item) {
                if (is_array($item)) {
                    $itemForKey = array_get($item, $valueKey);
                    if ($itemForKey and (is_string($itemForKey)
                        or is_numeric($itemForKey))) {
                        if ($closure and is_callable($closure)) {
                            $newArray[$itemForKey] = call_user_func($closure, $item, $valueKey);
                        } else {
                            $newArray[$itemForKey] = $item;
                        }
                    }
                }
            }
        }
        unset($array, $valueKey, $closure);

        return $newArray;
    }
}

if (! function_exists('__canAccess')) {
    /**
     * Check if access available
     *
     * @param  string  $accessId
     * @return bool.
     *-------------------------------------------------------- */
    function __canAccess($accessId = null)
    {
        if (
            YesAuthority::check($accessId) === true
            or YesAuthority::isPublicAccess($accessId)
        ) {
            return true;
        }

        return false;
    }
}

if (! function_exists('__canPublicAccess')) {
    /**
     * Check if access available
     *
     * @param  string  $accessId
     * @return bool.
     *-------------------------------------------------------- */
    function __canPublicAccess($accessId = null)
    {
        return YesAuthority::isPublicAccess($accessId);
    }
}

/**
 * listen Query events
 *---------------------------------------------------------------- */
if ((config('app.debug', false) == true)
    and config('laraware.app_db_log', false) == true
) {
    Event::listen('Illuminate\Database\Events\QueryExecuted', function ($event) {
        $bindings = $event->bindings;

        if (count($bindings) > 0) {
            // Format binding data for sql insertion
            foreach ($bindings as $i => $binding) {
                if ($binding instanceof \DateTime) {
                    $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } elseif (is_string($binding)) {
                    $bindings[$i] = "'$binding'";
                }
            }

            $clogItems['SQL__Bindings'] = implode(', ', $bindings);
        }

        // Insert bindings into query
        $query = str_replace(['%', '?'], ['%%', '%s'], $event->sql);
        $query = vsprintf($query, $bindings);

        $clogItems = ['SQL__Query' => $query];

        $clogItems['SQL__TimeTaken'] = $event->time;

        __clog($clogItems);
    });
}

if (! function_exists('updateCreateArrayFileItem')) {
    /**
     * Update config file
     *
     * @param  string  $configFile  - without .php
     * @param  mixed  $itemName
     * @param  mixed  $itemValue
     * @return mixed.
     *-------------------------------------------------------- */
    function updateCreateArrayFileItem($configFile, $itemName, $itemValue, $options = [])
    {
        $actualFileName = $configFile.'.php';
        if (! file_exists($actualFileName)) {
            $fh = fopen($actualFileName, 'a');
            fwrite($fh, '<?php
    return [];');
            fclose($fh);
        }

        $options = array_merge([
            'prepend_comment' => '',
        ], $options);

        $configFileArray = require $actualFileName;
        $updatedArray = array_set($configFileArray, $itemName, $itemValue);
        $arrayString = '<?php
            '.$options['prepend_comment'].'
    return ';
        $arrayString .= var_export($configFileArray, true).';';

        /*  config([
                $configFile => $configFileArray
            ]); */

        file_put_contents($actualFileName, $arrayString);

        return $updatedArray;
    }
}

if (! function_exists('arraySetAndGet')) {
    /**
     * Set the array item using get with replaced values
     * default is set array value
     *
     * @param  array  $setArray
     * @param  array  $getArray
     * @param  array  $replaceValues
     * @return array
     */
    function arraySetAndGet(&$setArray = [], &$getArray = [], $replaceValues = [])
    {
        $replaceValues = __nestedKeyValues($replaceValues);
        foreach ($replaceValues as $key => $value) {
            array_set($setArray, $key, array_get($getArray, $value, array_get($setArray, $key)));
        }
        unset($replaceValues);

        return $setArray;
    }
}

if (! function_exists('arrayFilterRecursive')) {
    /**
     * Remove the blank & null value elements from array recursively
     * Note: Resulting array items won't be replaced by blank or null items
     *
     * @uses ['__key__' => 2]
     *
     * @return array
     */
    function arrayFilterRecursive(array $array)
    {
        $newArray = [];
        foreach ($array as $arrayKey => $arrayValue) {
            // if it is array call again
            if (is_array($arrayValue)) {
                $getInternals = arrayFilterRecursive($arrayValue);
                // store if its not empty
                if (! empty($getInternals)) {
                    $newArray[$arrayKey] = $getInternals;
                }
                // check iff the item is not blank or null
            } elseif (($arrayValue !== null) and (trim($arrayValue) !== '')) {
                $newArray[$arrayKey] = $arrayValue;
            }
        }
        // unset non-required items
        unset($array);

        return $newArray;
    }
}
if (! function_exists('arrayExtend')) {
    /**
     * Extended array by other like jquery extends
     *
     * @return array
     */
    function arrayExtend(array $array, array $otherArray)
    {
        return array_replace_recursive(
            $array,
            // remove blank value items
            arrayFilterRecursive($otherArray)
        );
    }
}

if (! function_exists('arrayStringReplace')) {
    /**
     * Replace te string based keys within the array eg. __xyz__ change abc
     *
     * @param  array  $otherArray
     * @return array
     */
    function arrayStringReplace(array $array, array $updates)
    {
        return json_decode(
            // replace the items in array
            strtr(
                // convert to string
                json_encode($array),
                // changes array
                $updates
            ),
            true
        );
    }
}

if (! function_exists('updateClientModels')) {
    /**
     * Add items to update client models to response
     * it add client_models array to response on which
     * client update their models works fine with alpineJS models
     *
     * @param  string  $processType  - extend data to x-data
     * @return void
     *
     * @since 1.8.20 - 19 APR 2021
     *
     */
    function updateClientModels(array $items, ?string $processType = null)
    {
        // only on ajax request
        if (Request::ajax() === true) {
            $existingEntries = config('__update_client_models', []);
            if ($processType) {
                foreach ($items as $itemKey => $itemValue) {
                    if(!isset($existingEntries["@{$itemKey}"]) and !starts_with($itemKey, ['@'])) {
                        $items["@{$itemKey}"] = $processType;
                    }
                }
                $items["__{$processType}__"] = true;
            }
            config([
                '__update_client_models' => array_merge($existingEntries, $items),
            ]);
            unset($existingEntries);
        }

        return true;
    }
}

if (! function_exists('isExternalApiRequest')) {
    /**
     * Check if external api request
     *
     * @since 1.17.41 - 26 MAR 2024
     *
     * @return bool
     */
    function isExternalApiRequest()
    {
        return (bool) request()->headers->get('x-external-api-request', false);
    }
}

if (! function_exists('processExternalApiResponse')) {
    /**
         * API Responses
         *
         * @param EngineResponse|array $processReaction
         * @param array $data
         *
         * @since 1.17.41 - 26 MAR 2024
         * @return \Illuminate\Http\JsonResponse
         */
    function processExternalApiResponse($processReaction, $data = []): \Illuminate\Http\JsonResponse
    {
        if(is_array($processReaction) === true) {
            return response()->json([
                'result' => $processReaction['result'],
                'message' => $processReaction['message'],
                'data' => $data,
            ]);
        }
        return response()->json([
            'result' => $processReaction->success() ? 'success' : 'failed',
            'message' => $processReaction->message(),
            'data' => $data,
        ]);
    }
}
if (! function_exists('abortIf')) {
    /**
     * Implemented to handle ajax request
     * Throw an HttpException with the given data if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @since 1.9.25 - 04 JUN 2021.
     *
     * @updated 1.15.39 - 05 FEB 2024
     */
    function abortIf($boolean, $code = 404, $message = '', array $headers = [])
    {
        if ($boolean) {
            if ($code === 0) {
                $code = 400;
            }
            if(isExternalApiRequest()) {
                if (! headers_sent()) {
                    header('Content-Type: application/json');
                    http_response_code($code);
                }
                exit(json_encode([
                    'result' => 'failed',
                    'message' => $message,
                    'data' => [],
                ]));
            } elseif (Request::ajax() === false) {
                abort($code, $message, $headers);
            }
            if (! headers_sent()) {
                http_response_code($code);
            }
            exit(json_encode(array_merge(__response([], 2), [ // debug reaction
                'message' => $message ? $message : (function_exists('__tr') ? __tr('Operation aborted, may invalid request') : __('Operation aborted, may invalid request')),
                'data' => [],
            ])));
        }
    }
}
if (! function_exists('dispatchStreamEventData')) {
    /**
     * This function send data to browser using system ajax functionality
     * needs to use data-event-stream-update to true
     * then you can subscribe dispatched event
     * eg.
     * $(document).on('onLoginEventTest', function(xyz, dataResponse) { });
     * Known issues: does not set cookie, does not update csrf token
     *
     * @param  string  $eventName
     * @param  mixed  $data
     * @return void
     *
     * @since 1.10.32 - 21 SEP 2023
     */
    function dispatchStreamEventData($eventName, $data = [])
    {
        if (request()->hasHeader('X-Event-Stream-Update')) {
            if (! headers_sent()) {
                header('Content-type: text/html; charset=utf-8');
                header('Cache-Control: no-cache');
                header('Connection: keep-alive');
                // Disable buffering on Nginx
                header('X-Accel-Buffering: no'); // very important for nginx server
            }
            echo json_encode([
                'event' => $eventName,
                'data' => $data,
            ]);
            flush();
            // ob_flush only if it has active buffer
            if(ob_get_length()) {
                ob_flush();
            }
            usleep(5000);
            echo json_encode([
                'event' => null,
                'data' => null,
            ]);
            flush();
            // ob_flush only if it has active buffer
            if(ob_get_length()) {
                ob_flush();
            }
            usleep(5000);
        }
    }
}
if (! function_exists('updateClientModelsViaEvent')) {
    /**
     * Update Client Model via Streamed event response
     * Make sure your request element should have data attribute to data-event-stream-update as true
     * OR request options should have eventStreamUpdate to true
     *
     * @param array $data
     * @param string|null $processType - extend data to x-data
     *
     * @return void
     *
     * @since 1.11.33 - 23 APR 2024
     */
    function updateClientModelsViaEvent(array $data, ?string $processType = null)
    {
        if ($processType) {
            $data["__{$processType}__"] = true;
        }
        dispatchStreamEventData('__update_client_models', $data);
    }
}
if (! function_exists('updateProgressTextModel')) {
    /**
     * Update Progress text model via event
     * Make sure your request element should have data attribute to data-event-stream-update as true
     * OR request options should have eventStreamUpdate to true
     *
     * @param  string  $text
     * @return void
     *
     * @since 1.12.33 - 23 SEP 2023
     */
    function updateProgressTextModel($text)
    {
        dispatchStreamEventData('__update_client_models', [
            'lwProgressText' => $text,
        ]);
        usleep(100000);
    }
}

/**
 * In memory cache using config functionality so the repeated data abstraction can be avoided
 *
 * @param  string  $cacheKey
 * @param  mixed  $dataToCache
 * @return mixed
 *
 * @since  1.13.36 - 05 JAN 2024
 *---------------------------------------------------------------- */
if (! function_exists('viaFlashCache')) {
    function viaFlashCache(string $cacheKey, $originalData = null)
    {
        // fetch cached app settings from config
        $data = config("__cached__$cacheKey", null);
        if ($data === null) {
            if (! is_string($originalData) and is_callable($originalData)) {
                $data = call_user_func($originalData);
            } else {
                // if not available retrieve from original source
                $data = $originalData;
            }
            // check if its not running in console
            if (! app()->runningInConsole()) {
                // store cache in config as in memory
                config([
                    "__cached__$cacheKey" => $data,
                ]);
            }
        }

        return $data;
    }
}

/**
 * remove the config data stored
 *
 * @param  string  $cacheKey
 * @return bool
 *
 * @since  1.14.36 - 05 JAN 2024
 *---------------------------------------------------------------- */
if (! function_exists('emptyFlashCache')) {
    function emptyFlashCache(string $cacheKey)
    {
        // check if its not running in console
        if (! app()->runningInConsole()) {
            // store cache in config as in memory
            config([
                "__cached__$cacheKey" => null,
            ]);
        }

        return true;
    }
}

/**
 * Check if the app is in demo mode or not
 * based on the env value IS_DEMO_MODE
 *
 * @return bool
 *
 * @since  1.15.36 - 29 JAN 2024
 * @updated  1.17.43 - 11 APR 2024
 *-------------------------------------------------------- */
if (! function_exists('isDemo')) {
    function isDemo()
    {
        $isDemoModeOn = config('laraware.is_demo_mode', false) == true;
        if($isDemoModeOn) {
            $demoAccountAccessRequestSecretKey = request()->demo_account_access_secret_key;
            $configAccountAccessSecretKey = config('laraware.demo_account_access_secret_key', null);
            if($demoAccountAccessRequestSecretKey and $configAccountAccessSecretKey and ($demoAccountAccessRequestSecretKey === $configAccountAccessSecretKey)) {
                session([
                    '__demoAccountAccessAllowed' => true
                ]);
            }
            if(session('__demoAccountAccessAllowed') === true) {
                return false;
            }
        }
        return $isDemoModeOn;
    }
    // initial call
    isDemo();
}
