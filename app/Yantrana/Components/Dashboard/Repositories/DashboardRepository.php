<?php

/**
 * VendorRepository.php - Repository file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Dashboard\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Category\Models\ItemModel;

class DashboardRepository extends BaseRepository
{
    public function fetchItItems()
    {
        return ItemModel::join('categories', 'items.categories__id', '=', 'categories._id')
            ->select('items.*', 'categories.vendors__id as vendor')
            ->where([
                'items.status' => 1,
                'categories.vendors__id' => getVendorId(),
            ])
            ->count();
    }

    public function outOfStockItemsCount()
    {
        return ItemModel::join('categories', 'items.categories__id', '=', 'categories._id')
            ->select('items.*', 'categories.vendors__id as vendor')
            ->where([
                'items.status' => 1,
                'categories.vendors__id' => getVendorId(),
                'items.is_out_of_stock' => 1,
            ])->count();
    }
}
