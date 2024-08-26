<?php

/**
 * PageRepository.php - Repository file
 *
 * This file is part of the Page component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Page\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Page\Interfaces\PageRepositoryInterface;
use App\Yantrana\Components\Page\Models\PageModel;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = PageModel::class;

    /**
     * Fetch page datatable source
     *
     * @return mixed
     *---------------------------------------------------------------- */
    public function fetchPageDataTableSource()
    {
        // basic configurations for dataTables data
        $dataTableConfig = [
            // searchable columns
            'searchable' => [
                'title',
                'slug',
                'content',
                'status',

            ],
        ];

        // get Model result for dataTables
        return PageModel::dataTables($dataTableConfig)->toArray();
    }

    /**
     * Delete $page record and return response
     *
     * @param  object  $inputData
     * @return mixed
     *---------------------------------------------------------------- */
    public function deletePage($page)
    {
        // Check if $page deleted
        if ($page->deleteIt()) {
            // if deleted
            return true;
        }

        // if failed to delete
        return false;
    }

    /**
     * Store new page record and return response
     *
     * @param  array  $inputData
     * @return mixed
     *---------------------------------------------------------------- */
    public function storePage($inputData)
    {
        // prepare data to store
        $keyValues = [
            'title',
            'slug',
            'content' => $inputData['description'],
            'show_in_menu'=> (isset($inputData['show_in_menu']) and ($inputData['show_in_menu'] == 'on')) ? 1 : 0,
            'status' => (isset($inputData['status']) and ($inputData['status'] == 'on')) ? 1 : 0,
            'type' => 1,
            // 'vendors__id' => getUserID(),
        ];
        // create new model instance
        $newPage = new PageModel;

        // Check if task testing record added then return positive response
        if ($newPage->assignInputsAndSave($inputData, $keyValues)) {
            // if added record successfully
            return $newPage;
        }

        // if failed to add record
        return false;
    }

    /**
     * Update page record and return response
     *
     * @param  object  $page
     * @param  array  $inputData
     * @return bool
     *---------------------------------------------------------------- */
    public function updatePage($page, $inputData)
    {
        // Check if page updated then return positive response
        if ($page->modelUpdate($inputData)) {
            return true;
        }

        return false;
    }

    public function fetchBySlugVendor(string $pageSlug)
    {
        return $this->primaryModel::where([
            'slug' => $pageSlug,
            'vendors__id' => getPublicVendorId(),
        ])->first();
    }

   
}
