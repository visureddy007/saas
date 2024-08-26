<?php

namespace App\Yantrana\Base;

use App\Yantrana\__Laraware\Core\CoreModel;

abstract class BaseModel extends CoreModel
{
    /**
     * The custom primary key.
     *
     * @var string
     *----------------------------------------------------------------------- */
    protected $primaryKey = '_id';

    /**
     * The generate UID or not
     *
     * @var string
     *----------------------------------------------------------------------- */
    protected $isGenerateUID = true;

    /**
     * table sorting, pagination and searching
     *
     * @return mixed
     */
    public function scopeCustomTableOptions($query, $dataTablesConfig = [])
    {
        $params = request()->all();

        $searchableColumns = isset($dataTablesConfig['searchable'])
            ? $dataTablesConfig['searchable'] : null;

        // Searching
        if (isset($params['searchQuery']) && ! __isEmpty($searchableColumns)) { // check for query parameter
            if (is_array($params['searchQuery'])) {
                $query->shodhArray($params['searchQuery'], $searchableColumns);
            } else {
                $query->shodh($params['searchQuery'], $searchableColumns);
            }
        }

        $sortBy = $this->table.'.'.$this->primaryKey;
        // Order By
        $sortOrder = 'desc';

        //Sorting
        if (isset($params['sortBy']) && isset($params['sortOrder'])) { // check for query parameter
            $sortBy = $params['sortBy'];
            $sortOrder = $params['sortOrder'];
        }

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

        //Pagination
        if (array_get($params, 'pageSize', false)) {
            $paginationCount = $params['pageSize'];

            return $query->orderBy($sortBy, $sortOrder)->paginate($paginationCount);
        }

        return $query->orderBy($sortBy, $sortOrder)->paginate();
    }
}
