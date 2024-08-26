<?php

namespace App\Yantrana\Base;

use App\Yantrana\__Laraware\Core\CoreEngine;
/**
 * Base Engine
 *
 * base engine for Angulara applications
 *
 *--------------------------------------------------------------------------- */

use Request;

abstract class BaseEngine extends CoreEngine
{
    /**
     * Load DataTable Helper 0.2.1 - 03 JUN 2015
     *
     * helper function to load datatable.
     *
     * @param  array  $data  - for request response
     * @return void.
     *-------------------------------------------------------- */
    public function customTableResponse($sourceData, $dataFormat = [], $options = [])
    {
        $data = [];

        $paginationLinks = sprintf($sourceData->links('pagination::bootstrap-4'));

        $remainingItems = $sourceData->total() - $sourceData->lastItem();
        $perPage = $sourceData->perPage();

        $paginationData = [
            'currentPage' => $sourceData->currentPage(),
            'lastPage' => $sourceData->lastPage(),
            'nextPageURL' => $sourceData->nextPageUrl(),
            'hasMorePages' => $sourceData->hasMorePages(),
            'remainingItems' => (int) $remainingItems,
            'lastItem' => $sourceData->lastItem(),
            'perPage' => (int) $perPage,
            'count' => $sourceData->count(),
            'total' => $sourceData->total(),
            //'paginationLinks'     => sprintf($sourceData->links("pagination::bootstrap-4"))
        ];

        $rawData = $sourceData->toArray();
        $enhancedData = [];

        foreach ($rawData['data'] as $key) {
            $newDataFormat = [];

            if (! empty($dataFormat)) {
                foreach ($dataFormat as $dataItemKey => $dataItemValue) {
                    if (is_numeric($dataItemKey)) {
                        $newDataFormat[$dataItemValue] = $key[$dataItemValue];
                    } elseif (! is_string($dataItemValue) and is_callable($dataItemValue)) {
                        $newDataFormat[$dataItemKey] = call_user_func($dataItemValue, $key);
                    } else {
                        $newDataFormat[$dataItemKey] = $key[$dataItemValue];
                    }
                }
            } else {
                $newDataFormat = $key;
            }

            $primaryKey = array_key_exists('_id', $key) ? '_id' : 'id';
            $enhancedData[] = $newDataFormat;
        }

        $dataTablesData = [
            'data' => $enhancedData,
            'paginationLinks' => $paginationLinks,
            'paginationData' => $paginationData,
            'pageInfo' => [
                'from' => isset($rawData['from']) ? $rawData['from'] : 0,
                'to' => isset($rawData['to']) ? $rawData['to'] : 0,
                'total' => $rawData['total'],
            ],
        ];

        $data['response_token'] = (int) Request::get('fresh');

        $data = array_merge($data, $dataTablesData);

        if (! empty($options)) {
            $data['_options'] = $options;
        }

        unset($enhancedData, $rawData, $sourceData, $dataFormat, $dataTablesData);

        return __apiResponse($data);
    }
}
