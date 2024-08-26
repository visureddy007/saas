<?php

namespace App\Yantrana\__Laraware\Core;

use App\Http\Controllers\Controller;
use Exception;
use View;

/**
 * CoreController - 0.4.6 - 03 NOV 2022
 *
 *-------------------------------------------------------- */
abstract class CoreController extends Controller
{
    /**
     * Force Secure output
     *
     * @var bool
     *----------------------------------------------------------------------- */
    protected $forceSecureResponse = false;

    /**
     * Load view helper
     *
     * @param  string  $viewName  - View Name
     * @param  array  $data  - Array of data if needed
     * @return array
     *-------------------------------------------------------------------------- */
    public function loadView($viewName, $data = [], $options = [])
    {
        $options = array_merge([
            'compress_page' => true,
        ], $options);

        $output = View::make($viewName, $data)->render();

        if ((config('app.debug', false) === false)
            and $options['compress_page'] === true
        ) {
            $filters = [
                '/<!--([^\[|(<!)].*)/' => '',  // Remove HTML Comments (breaks with HTML5 Boilerplate)
                '/(?<!\S)\/\/\s*[^\r\n]*/' => '',  // Remove comments in the form /* */
                '/\s{2,}/' => ' ', // Shorten multiple white spaces
                '/(\r?\n)/' => '',  // Collapse new lines
            ];

            $output = preg_replace(
                array_keys($filters),
                array_values($filters),
                $output
            );
        }

        $clogSessItemName = '__clog';
        if (! empty(config('app.'.$clogSessItemName, []))) {
            $responseData = [
                '__dd' => true,
                '__clogType' => 'NonAjax',
                $clogSessItemName => config('app.'.$clogSessItemName),
            ];

            //reset the __clog items in session
            config(['app.'.$clogSessItemName => []]);
            $output = $output.'<script type="text/javascript"> if(__globals === undefined) { var __globals = {}; }; __globals.clog('.json_encode($responseData).');</script>';
        }

        // update client models as per response
        // @since 0.4.5
        $updateClientModels = config('__update_client_models', []);
        if (! empty($updateClientModels)) {
            config([
                '__update_client_models' => [],
            ]);
            $output = $output.'<script type="text/javascript">(function(){"use strict"; __DataRequest.updateModels('.json_encode($updateClientModels).'); })();</script>';
            unset($updateClientModels);
        }

        return $output;
    }

    /**
     * Process response & send API response
     *
     * @param  int  $engineReaction  - Engine reaction
     * @param  array  $responses  - Response Messages as per reaction code
     * @param  array  $data  - Additional Data for success
     * @param  bool  $appendEngineData
     * @param  int  $httpCode  - @since 0.2.4 - 22 APR 2021
     * @return array
     *---------------------------------------------------------------- */
    public function processResponse(
        $engineReaction,
        $messageResponses = [],
        $data = [],
        $appendEngineData = false,
        $httpCode = null
    ) {
        // forced to be secured
        if ($this->forceSecureResponse === true) {
            return __secureProcessResponse(
                $engineReaction,
                $messageResponses,
                $data,
                $appendEngineData,
                $httpCode
            );
        }

        return __processResponse(
            $engineReaction,
            $messageResponses,
            $data,
            $appendEngineData,
            $httpCode
        );
    }

    /**
     * Process response & send API encrypted response
     *
     * @param  int  $engineReaction  - Engine reaction
     * @param  array  $responses  - Response Messages as per reaction code
     * @param  array  $data  - Additional Data for success
     * @param  bool  $appendEngineData
     * @param  int  $httpCode  - @since 0.2.4 - 22 APR 2021
     * @return array
     *---------------------------------------------------------------- */
    public function secureProcessResponse(
        $engineReaction,
        $messageResponses = [],
        $data = [],
        $appendEngineData = false,
        $httpCode = null
    ) {
        return __secureProcessResponse(
            $engineReaction,
            $messageResponses,
            $data,
            $appendEngineData,
            $httpCode
        );
    }

    /**
     * Get Engine Data
     *
     * @since - 0.3.4 - 01 JUN 2021
     *
     * @return mixed
     *-------------------------------------------------------------------------- */
    public function engineData($engineReaction, $item = null, $default = null)
    {
        if (array_has($engineReaction, 'data') === false) {
            throw new Exception('Invalid Engine Reaction', 1);
        }
        if ($item) {
            $item = '.'.$item;
        }

        return array_get($engineReaction, 'data'.$item, $default);
    }

    /**
     * Get Engine Message
     *
     * @since - 0.4.4 - 01 JUN 2021
     *
     * @return string
     *-------------------------------------------------------------------------- */
    public function engineMessage($engineReaction, $default = '')
    {
        return array_get($engineReaction, 'message', $default);
    }
}
