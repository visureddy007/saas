<?php

namespace App\Yantrana\__Laraware\Core;

/**
 *  __Laraware 1.x.x - Core Model - 1.7.10 - 21 DEC 2022
 *
 * Base Model for Laravel applications
 *
 *
 * Dependencies:
 *
 * Laravel     5.2 +  - http://laravel.com
 *
 *
 *-------------------------------------------------------- */

use Cache;
use Datetime;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Request;
use YesSecurity;

abstract class CoreModel extends Eloquent
{
    public static function boot()
    {
        parent::boot();

        // Generate UID if required
        static::creating(function ($model) {
            if ($model->isGenerateUID) {
                $model->{$model->UIDKey} = YesSecurity::generateUid();
            }

            foreach ($model->getAttributes() as $key => $value) {
                // process JSON data if exists
                if (
                    array_key_exists($key, $model->getAttributes())
                    and array_key_exists($key, $model->jsonColumns)
                ) {
                    // get verified data
                    $model->{$key} = $model->verifyAndUpdateJsonColumnData($key, $model->{$key}, false);
                }
            }
        });

        static::updating(function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                // process JSON data if exists
                if (
                    array_key_exists($key, $model->getAttributes())
                    and array_key_exists($key, $model->jsonColumns)
                ) {
                    // get verified data
                    $model->{$key} = $model->verifyAndUpdateJsonColumnData($key, $model->{$key});
                }
            }
        });

        // clear cache if exist
        static::saved(function ($model) {
            $model->clearCacheItems();
        });

        // clear cache if exist
        static::deleted(function ($model) {
            $model->clearCacheItems();
        });
    }

    /**
     * Datatable Result counts also its max result per request.
     *
     * @var string
     *----------------------------------------------------------------------- */
    protected $maxDataTableResultCount = 100;

    /**
     * The generate UID or not.
     *
     * @var string
     *----------------------------------------------------------------------- */
    protected $isGenerateUID = false;

    /**
     * The UID Name.
     *
     * @var string
     *----------------------------------------------------------------------- */
    protected $UIDKey = '_uid';

    /**
     * Caching Ids related to this model which may need to clear on add/update/delete.
     *
     * @var array
     *----------------------------------------------------------------------- */
    protected $cacheIds = [];

    /**
     * Let the system knows Text columns treated as JSON
     *
     * @var array
     *----------------------------------------------------------------------- */
    protected $jsonColumns = [];

    /**
     * Skip JSON Process Protocol
     *
     * @var bool
     *----------------------------------------------------------------------- */
    protected $skipJsonColumnProtocol = false;

    /**
     * Match provided array field values with existing -
     * model field values and update model.
     *
     * @param  array  $inputs  - for update existing model field values
     * @return boolean/array
     *----------------------------------------------------------------------- */
    public function modelUpdate(array $inputs)
    {
        $updatedColumns = [];

        foreach ($inputs as $key => $value) {
            // process JSON data if exists
            if (
                array_key_exists($key, $this->getOriginal())
                and array_key_exists($key, $this->jsonColumns)
            ) {
                $value = $this->verifyAndUpdateJsonColumnData($key, $value);
            }

            /**
             * Match provided array field with existing -
             * model field and also check values.
             *------------------------------------------------------------------------ */
            if (
                array_key_exists($key, $this->getOriginal())
                and $this->{$key} != $value
            ) {
                // assign value
                $this->{$key} = $value;

                $updatedColumns[$key] = $value;
            }
        }

        if (! empty($updatedColumns)) {
            if ($this->save()) {
                return $updatedColumns;
            }

            return false;
        }

        return false;
    }

    /**
     * Notes:: this function optimized for __DataStore datatable 0.5.x.
     *
     * This function manage datatable ajax request. Function -
     * take query and manage all things need in datatable.
     *
     * @param  array  $dataTablesConfig  - for custom field alias
     * @param  object  $dataTablesConfig  - for database table query scope
     * @return eloquent object
     *------------------------------------------------------------------------ */
    public function scopeDataTables($query, array $dataTablesConfig = [])
    {
        $inputData = Request::all();

        $columns = $inputData['columns'];
        $order = isset($inputData['order']) ? $inputData['order'] : null;

        $sortBy = $this->table.'.'.$this->primaryKey;
        $sortOrder = 'asc';

        // if order not set
        if (! empty($order)) {
            $sortBy = $columns[$order[0]['column']]['data'];
            $sortOrder = $order[0]['dir'];
        }

        $receivedLength = $inputData['length'];

        $perPage = ($receivedLength <= 0)
            ? $this->maxDataTableResultCount : $receivedLength;

        $start = $inputData['start'] / $perPage;

        /*  Field aliases
        --------------------------------------------------------------------- */

        $fieldAlias = [];

        // if dataTablesConfig fieldAlias exist
        if (! empty($dataTablesConfig['fieldAlias'])) {
            foreach ($dataTablesConfig['fieldAlias'] as $key => $value) {
                $fieldAlias[$key] = $value;

                // set fieldAlias as sortable
                if ($key == $sortBy) {
                    $sortBy = $value;
                }
            }
        }

        /* DataTable Search
        --------------------------------------------------------------------- */

        $search = $inputData['search'];
        $searchableColumns = isset($dataTablesConfig['searchable'])
            ? $dataTablesConfig['searchable'] : null;

        $query->shodh($search['value'], $searchableColumns);

        /*
         if search from datatables ends
        --------------------------------------------------------------------- */

        // check to see if maxDataTableResultCount has been set or not
        $applicableResultCount = (isset($this->maxDataTableResultCount)
            and is_int($this->maxDataTableResultCount))
            ? $this->maxDataTableResultCount : 100;

        // check if we received per page request from browser
        $applicableResultCount = $receivedLength
            ? $perPage : $applicableResultCount;

        // check if per page request from browser is greater
        // than maxDataTableResultCount
        $applicableResultCount =
            ($applicableResultCount > $this->maxDataTableResultCount)
            ? $this->maxDataTableResultCount
            : $applicableResultCount;

        if (! $sortOrder) {
            $sortOrder = 'asc';
        }

        // finally prepare query
        return $query->orderBy($sortBy, $sortOrder)
            ->paginate($applicableResultCount);
    }

    /**
     * Search in the Model.
     *
     * @param  array  $searchTerm  - search term
     * @param  object  $searchableColumns  - columns to search
     * @return eloquent object
     *
     * @since  0.2.0 - 30 NOV 2015
     *------------------------------------------------------------------------ */
    public function scopeShodh($query, $searchTerm, $searchableColumns)
    {
        if (
            ! empty($searchTerm)
            and ! empty($searchableColumns) and is_array($searchableColumns)
        ) {
            $query->where(function ($whereQuery) use ($searchableColumns, $searchTerm) {
                foreach ($searchableColumns as $searchableFieldName) {
                    $whereQuery->orWhere(
                        $searchableFieldName,
                        'like',
                        '%'.$searchTerm.'%'
                    );
                }
            });
        }

        return $query;
    }

    /**
     * Assign values to inputs & save model.
     *
     * @param  array  $input  - input array
     * @param  array  $keyValues  - assign constraints
     * @return bool
     *------------------------------------------------------------------------ */
    public function assignInputsAndSave($input, $keyValues)
    {
        foreach ($keyValues as $key => $value) {
            if (is_string($key)) {
                // process JSON data if exists
                if (array_key_exists($key, $this->jsonColumns)) {
                    // get verified data
                    $this->{$key} = $this->verifyAndUpdateJsonColumnData($key, $value, false);
                } else {
                    $this->{$key} = $value;
                }
            } else {
                if (isset($input[$value])) {
                    $this->{$value} = $input[$value];
                }
            }
        }

        unset($input, $keyValues);

        return $this->save();
    }

    /**
     * Prepare uid, timestamp etc & insert.
     *
     * @param  array  $input  - input array
     * @param bool/string $returnColumn - if you want ids of the inserted records
     * @return bool|mixed|array
     *------------------------------------------------------------------------ */
    public function prepareAndInsert($input, $returnColumn = false)
    {
        $timestamp = new Datetime();
        $preparedRecords = [];
        $itemUids = [];

        // check if needs return item & it should be string or boolean
        if ($returnColumn) {
            if (in_array(gettype($returnColumn), ['boolean', 'string']) === false) {
                throw new Exception('prepareAndInsert - Only boolean or string value is accepted.');
            }
        }

        // check input has contain of array items
        if ((is_array($input) === false) or (isset($input[0]) === false or is_array($input[0]) === false)) {
            throw new Exception('prepareAndInsert - Input should contain array items!!');
        }

        foreach ($input as $item) {
            foreach ($item as $itemKey => $itemValue) {
                // process JSON data if exists
                if (array_key_exists($itemKey, $this->jsonColumns)) {
                    $item[$itemKey] = json_encode($this->verifyAndUpdateJsonColumnData($itemKey, $itemValue, false));
                }
            }

            if ($this->isGenerateUID) { // generate uid if required
                if (! isset($item[$this->UIDKey]) or ! $item[$this->UIDKey]) {
                    $item[$this->UIDKey] = YesSecurity::generateUid();
                }
                $itemUids[] = $item[$this->UIDKey];
            }

            // event for prepare and insert
            $item = $this->onPrepareAndInserting($item);
            // check if return item is array
            if (! is_array($item)) {
                throw new Exception('onPrepareAndInserting should return item array');
            }

            if ($this->timestamps) { // add timestamps if allowed
                $item['created_at'] = $timestamp;
                $item['updated_at'] = $timestamp;
            }

            $preparedRecords[] = $item;
        }

        // insert the items
        $insertResult = $this->insert($preparedRecords);

        // clear cache items if available
        $this->clearCacheItems();

        unset($input, $timestamp, $preparedRecords);

        //check if return item required ids or selected column
        if (($returnColumn) and ($this->isGenerateUID) and ($insertResult === true)) {
            // item to get return
            $itemToPluck = ($returnColumn === true) ? $this->primaryKey : $returnColumn;

            // get items from db using uids
            $result = $this->whereIn($this->UIDKey, $itemUids)->get([
                $itemToPluck,
            ]);

            // check if pluck method is available which should be in Laravel 5.2+
            if (method_exists($result, 'pluck')) {
                return __ifIsset($result, $result->pluck([$itemToPluck]))->toArray();
            }

            // if not use earlier lists method
            return __ifIsset($result, $result->lists([$itemToPluck]))->toArray();
        }

        return $insertResult;
    }

    /**
     * Clear Cache using CacheIds.
     *
     * @param  array  $cacheIds
     * @param  bool  $isClearAll
     * @return bool
     */
    protected function clearCacheItems($cacheIds = [], $isClearAll = false)
    {
        if (! empty($cacheIds) and ! is_array($cacheIds)) {
            $cacheIds = [$cacheIds];
        }

        if ($isClearAll) {
            $cacheIds = array_merge($cacheIds, $this->cacheIds);
        }

        if (empty($cacheIds) === true) {
            $cacheIds = $this->cacheIds;
        }

        foreach ($cacheIds as $cacheId) {
            Cache::forget($cacheId);
        }

        return true;
    }

    /**
     * Alias of batchUpdate for static uses
     *
     * @param  array  $data  - Data to update along with index key passed
     *                       using 3rd parameter
     * @param  string  $index  - Index key
     * @param  array  $whereConditions  - Where condition with key & value @since 1.6.7 - 18 APR 2021
     * @return string.
     *-------------------------------------------------------- */
    public static function bunchUpdate(array $data, $index, $whereConditions = [])
    {
        $instance = new static();

        return $instance->batchUpdate($data, $index, $whereConditions);
    }

    /**
     * Batch Update.
     *
     * @param  array  $data  - Data to update along with index key passed
     *                       using 3rd parameter
     * @param  string  $index  - Index key
     * @param  array  $whereConditions  - Where condition with key & value @since 1.6.7 - 18 APR 2021
     * @return string.
     *-------------------------------------------------------- */
    public function batchUpdate(array $data, $index, $whereConditions = [])
    {
        if (empty($data) or empty($index)) {
            throw new Exception('Invalid data or index');
        }

        $getItemsToUpdate = [];
        $jsonColumnKeys = [];

        if (empty($this->jsonColumns) === false) {
            $columnKeys = [];

            foreach ($data as $dataItem) {
                $columnKeys = array_merge($columnKeys, array_keys($dataItem));
            }

            $columnKeys = array_unique($columnKeys);

            if (empty($columnKeys) === false) {
                foreach ($columnKeys as $columnKey) {
                    if (array_key_exists($columnKey, $this->jsonColumns)) {
                        $jsonColumnKeys[] = $columnKey;
                    }
                }

                if (empty($jsonColumnKeys) === false) {
                    $getItemsToUpdate = $this->whereIn($index, array_pluck($data, $index))->get(array_merge($jsonColumnKeys, [$index]));
                }
            }
        }

        $tableName = $this->table;

        $recordsUpdated = DB::transaction(function () use ($tableName, $data, $index, $jsonColumnKeys, $getItemsToUpdate, $whereConditions) {
            $rawQueryString = 'update '.$tableName.' SET ';
            $updateData = [];
            $ids = $when = [];
            $cases = '';

            //generate the WHEN statements from the set array
            foreach ($data as $key => $val) {
                foreach (array_keys($val) as $field) {
                    if ($field != $index) {
                        // process JSON data if exists
                        if ((empty($this->jsonColumns) === false)
                            and in_array($field, $jsonColumnKeys) === true
                        ) {
                            $existingItemData = $getItemsToUpdate->where($index, $val[$index])
                                ->first();
                            $val[$field] = json_encode(
                                $this->verifyAndUpdateJsonColumnData(
                                    $field,
                                    $val[$field],
                                    $existingItemData->{$field}
                                )
                            );
                        }

                        $when[$field][] = 'WHEN '.$index
                            .' = "'.$val[$index].'" THEN ? ';

                        $updateData[$field][] = $val[$field];
                        $ids[$field][] = DB::connection()->getPdo()->quote($val[$index]);
                    }
                }
            }

            if (empty($when) or empty($ids) or empty($updateData)) {
                throw new Exception('Invalid data passed');
            }

            //generate the case statements with the keys and values from the when array
            foreach ($when as $k => $v) {
                $cases .= "\n".$k.' = CASE '."\n";

                foreach ($v as $row) {
                    $cases .= $row."\n";
                }
                $cases .= 'ELSE '.$k.' END, ';
            }

            $rawQueryString .= substr($cases, 0, -2)."\n"; //remove the comma of the last case
            // old where in
            if (! empty($whereConditions)) {
                $rawQueryString .= ' WHERE '.$whereConditions['key'].' = ( '.$whereConditions['value'].' )';
            }

            return DB::update($rawQueryString, array_flatten($updateData));
        });

        // clear cache items if available
        $this->clearCacheItems();

        return $recordsUpdated;
    }

    /**
     * Prepare or insert shortcut
     *
     * @param  array  $data  - Data to update along with index key passed
     *                       using 2nd parameter
     * @param  string  $index  - Index key
     * @param  array  $whereConditions  - Where condition with key & value @since 1.6.7 - 18 APR 2021
     *
     * @since  1.5.0 - 10 JUN 2019
     *
     * @return string.
     *-------------------------------------------------------- */
    public static function bunchInsertUpdate(array $data, ?string $index = null, $whereConditions = [])
    {
        // verify data
        if (__isEmpty($data) or (is_array($data) === false)) {
            throw new Exception('invalid $data array');
        }
        // verify index
        if ($index and (is_string($index) === false)) {
            throw new Exception('invalid $index string');
        }

        // data containers
        $dataToUpdate = [];
        // collect indexes for updated
        $dataToUpdateIndexes = \array_filter(
            \array_pluck(
                $data,
                $index
            )
        );
        $dataToInsert = [];
        $dataToInsertWithIndex = [];
        //instance
        $instance = new static();
        // fetch to cross check existing data for update
        $existingResult = $instance->select($index);
        if (! empty($whereConditions)) {
            $existingResult->where($whereConditions['key'], $whereConditions['value']);
        }
        $existingResult->whereIn($index, $dataToUpdateIndexes)->get();
        // get indexes only
        $existedIndexes = $existingResult->pluck($index)->toArray();

        // loop through the data
        foreach ($data as $dataItem) {
            // verify data items
            if (__isEmpty($dataItem) or (is_array($dataItem) === false)) {
                throw new Exception('$data array array cannot be empty');
            }
            // determine whatever to update or insert
            if ($index and isset($dataItem[$index]) and (__isEmpty($dataItem[$index]) === false)) {
                if (in_array($dataItem[$index], $existedIndexes)) {
                    if (! empty($whereConditions)) {
                        $isFound = false;
                        if ($dataItem[$whereConditions['key']] === $whereConditions['value']) {
                            $isFound = true;
                        }
                        if ($isFound === true) {
                            // collect for update
                            $dataToUpdate[] = $dataItem;
                        }
                    } else {
                        // collect for update
                        $dataToUpdate[] = $dataItem;
                    }
                } else {
                    // collect for insert with indexes
                    $dataToInsertWithIndex[] = $dataItem;
                }
            } else {
                // collect for insert
                $dataToInsert[] = $dataItem;
            }
        }

        // as off the multiple queries using a transaction
        return DB::transaction(function () use ($dataToInsert, $dataToUpdate, $dataToInsertWithIndex, $index, $instance, $whereConditions) {
            // update count
            $updateCount = 0;
            // if data available sent for insert
            // and add to containers
            if (\__isEmpty($dataToInsert) === false) {
                if ($instance->prepareAndInsert($dataToInsert)) {
                    $updateCount++;
                }
            }

            // if data available sent for insert with index
            // and add to containers
            if (\__isEmpty($dataToInsertWithIndex) === false) {
                if ($instance->prepareAndInsert($dataToInsertWithIndex)) {
                    $updateCount++;
                }
            }

            // if data available sent for update
            // and add to containers
            if (\__isEmpty($dataToUpdate) === false) {
                if ($instance->batchUpdate($dataToUpdate, $index, $whereConditions)) {
                    $updateCount++;
                }
            }
            // lets free up the memory
            $data = $index = $dataToInsert = $dataToUpdate = $dataToInsertWithIndex = null;
            unset($data, $index, $dataToInsert, $dataToUpdate, $dataToInsertWithIndex);

            // get back
            return $updateCount >= 1;
        });
    }

    /**
     * Delete the model from the database & clear cache items.
     *
     * @param  object  $query  - query
     *
     * @since  0.5.0 - 14 JUL 2015 - Updated on 05 JAN 2017
     *------------------------------------------------------------------------ */
    public function scopeDeleteIt($query)
    {
        $originals = $query->getModel()->getOriginal();

        if (! empty($originals)) {
            $query->where($this->primaryKey, $originals[$this->primaryKey]);
        }

        if (empty($query->getQuery()->wheres)) {
            return false;
        }

        $resultQuery = $query->delete();
        // clear cache items if available
        $this->clearCacheItems();

        return $resultQuery;
    }

    /**
     * Process JSON Item
     * Please note this function doesn't care about defined JSON column structure, so please be careful
     *
     * @param  array  $query  - query
     *
     * @since  0.7.0 - 27 JAN 2017
     *------------------------------------------------------------------------ */
    public function scopeProcessJsonItem($query, $jsonColumn, $jsonItem, $callback)
    {
        // if no json column is set then no need to process further
        if ((empty($this->jsonColumns) === true)
            and array_key_exists($jsonColumn, $this->jsonColumns) === false
        ) {
            return $query;
        }

        // get the existing
        $columnData = is_array($this->{$jsonColumn})
            ? $this->{$jsonColumn}
            : collect($this->{$jsonColumn})->toArray();

        if (array_has($columnData, $jsonItem) === false) {
            return $query;
        }

        // get requested items
        $itemsFound = array_get($columnData, $jsonItem);
        // do the process
        $resultData = call_user_func($callback, $itemsFound);
        // set the resulted item
        array_set($columnData, $jsonItem, $resultData);
        // set for column data
        $this->{$jsonColumn} = $columnData;
        // prevent verifyAndUpdateJsonColumnData to process
        $this->skipJsonColumnProtocol = true;

        // return the query
        return $query;
    }

    /**
     * Verify JSON data structure and merge existing data with new validated data
     *
     * @param  string  $key  - JSON column key
     * @param  array  $value  - new values to be add/update
     * @param array/boolean - $updateRequest - is it update
     *                        or new/old existing data to merged
     *
     * @since  0.6.x - 28 NOV 2016
     *
     * @last-modified - 1.7.9 - 18 SEP 2021
     *
     * @return array
     *----------------------------------------------------------------------- */
    protected function verifyAndUpdateJsonColumnData($key, $value, $updateRequest = true)
    {
        // check if it is process request
        if ($this->skipJsonColumnProtocol === true) {
            return $value;
        }

        // if no json column is set then no need to process further
        if (empty($this->jsonColumns) === true) {
            return $value;
        }

        // set values as Array
        $value = collect($value)->toArray();
        // line up array values
        $tempUpdates = array_dot($value);
        // check the request type if not boolean it should be existing data array
        if (is_bool($updateRequest) === false) {
            $existingKeyData = $updateRequest;
        } else {
            $existingData = $updateRequest ? $this->getOriginal() : false;
            if (
                $existingData and isset($existingData[$key])
                and is_string($existingData[$key])
            ) {
                $existingKeyData = $updateRequest ? json_decode($existingData[$key], true) : [];
            } elseif (
                $existingData and isset($existingData[$key])
                and is_array($existingData[$key])
            ) {
                $existingKeyData = $updateRequest ? $existingData[$key] : [];
            } else {
                $existingKeyData = [];
            }
        }

        // get existing data & convert it to array
        $validData = collect($existingKeyData)->toArray();
        $itemKeyProcessed = [
            'array:extend' => [],
        ];

        $itemsToRemove = [];
        $itemsToRemoveByKeys = [];
        $itemsToRemoveWrapperByKeys = [];

        foreach ($tempUpdates as $tempItemKey => $tempItemValue) {
            // get defined column item
            $definedItem = array_get($this->jsonColumns, $key.'.'.$tempItemKey);
            // data to update
            $updateItem = array_get($value, $tempItemKey);
            // get update item datatype
            $updateItemType = strtolower(gettype($updateItem));
            // check if it is string and it matches with defined item datatype
            if (is_string($definedItem) and (strtolower($definedItem) === $updateItemType)) {
                // set the values
                array_set($validData, $tempItemKey, $tempItemValue);

                continue;
            }
            // check if it's not string and it's datatype matches with defined item datatype
            elseif ((is_string($definedItem) !== true) and gettype($definedItem) === $updateItemType) {
                // set the values
                array_set($validData, $tempItemKey, $tempItemValue);

                continue;
            } else {
                // if defined item is array so any values can be accepted for that particular item
                foreach (array_dot($this->jsonColumns) as $jsonKey => $jsonValue) {
                    $startWith = starts_with($key.'.'.$tempItemKey, $jsonKey);
                    $searchedItemKey = str_replace($key.'.', '', $jsonKey);
                    $searchedItemValue = array_get($value, $searchedItemKey);

                    if (($startWith === true)
                        and (__isEmpty($searchedItemValue) === false)
                    ) {
                        switch ($jsonValue) {
                            case 'array': // will replace it with new items
                                // set the values
                                array_set($validData, $searchedItemKey, array_get($value, $searchedItemKey));
                                break;

                            case 'array:extend': // will merge the items with existing

                                if (in_array($searchedItemKey, $itemKeyProcessed['array:extend'])) {
                                    break;
                                }

                                // get the existing data
                                $existingItemData = array_get($validData, $searchedItemKey);

                                if (! __isEmpty($existingItemData)) {
                                    array_set($validData, $searchedItemKey, arrayExtend($existingItemData, array_get($value, $searchedItemKey)));
                                } else {
                                    array_set($validData, $searchedItemKey, array_get($value, $searchedItemKey));
                                }

                                $itemKeyProcessed['array:extend'][] = $searchedItemKey;

                                break;
                        }
                    }
                }
            }
        }

        return $validData;
    }

    /**
     * Get the array of columns
     *
     * @return mixed
     *
     * @since  1.6.0 - 11 JUN 2019 - Added from Base
     */
    private function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    /**
     * Exclude an array of elements from the result.
     *
     * @return mixed
     *
     * @since  1.6.0 - 11 JUN 2019 - Added from Base
     */
    public function scopeSelectExcept($query, $columns)
    {
        $fetchColumns = empty($columns) ? [] : array_diff($this->getTableColumns(), (array) $columns);

        return empty($fetchColumns) ? $query : $query->select($fetchColumns);
    }

    /**
     * Exclude an array of elements from the result.
     *
     * @return mixed
     *
     * @since  1.6.0 - 11 JUN 2019 - Added from Base
     */
    public function scopeSelectOnly($query, $columns)
    {
        $fetchColumns = empty($columns) ? [] : array_intersect($this->getTableColumns(), (array) $columns);

        return empty($fetchColumns) ? $query : $query->select($fetchColumns);
    }

    /**
     * Get uid key name
     *
     * @return mixed
     *
     * @since  1.6.0 - 11 JUN 2019
     */
    public function getUidKeyName()
    {
        return $this->UIDKey;
    }

    /**
     *  Event: onPrepareAndInserting
     *  Write this function on required model it will be automatically called by CodeModel
     *  prepareAndInsert function
     *  You should return the same $item array to avoid any issues
     *
     * @return array
     */
    public function onPrepareAndInserting(array $item)
    {
        return $item;
    }
}
