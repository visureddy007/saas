<?php

/**
 * VendorRepository.php - Repository file
 *
 * This file is part of the Vendor component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Vendor\Repositories;

use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\Vendor\Interfaces\VendorRepositoryInterface;
use App\Yantrana\Components\Vendor\Models\VendorModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VendorRepository extends BaseRepository implements VendorRepositoryInterface
{
    /**
     * primary model instance
     *
     * @var object
     */
    protected $primaryModel = VendorModel::class;

    /**
     * Store Vendor into database
     *
     * @return object|bool
     */
    public function storeVendor(array $inputs = [])
    {
        return $this->storeIt($inputs);
    }

    /**
     * Fetch List of users
     *
     * @param    int || int $status
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchVendorsDataTableSource()
    {
        $dataTableConfig = [
            'searchable' => [
                'title',
                'fullName' => DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))"),
                'email',
                'username',
                'slug',
                'mobile_number',
            ],
        ];

        return $this->primaryModel::leftJoin('users', 'users.vendors__id', '=', 'vendors._id')
            ->select(
                __nestedKeyValues([
                    'vendors' => [
                        '_id',
                        '_uid',
                        'title',
                        'created_at',
                        'status',
                        'slug',
                    ],
                    'users' => [
                        '_id as userId',
                        'username as username',
                        'email',
                        'status as user_status',
                        'mobile_number',
                        DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) AS fullName"),
                    ],
                ])
            )
            ->dataTables($dataTableConfig)
            ->toArray();
    }

    /**
     * Fetch Record by slug
     *
     * @param  array  $names
     * @return eloquent collection object
     *---------------------------------------------------------------- */
    public function fetchBySlug($slug)
    {
        if (! $slug) {
            return null;
        }

        return $this->primaryModel::where([
            'slug' => $slug,
        ])->first();
    }

    /**
     * FetchIt Vendor
     *
     * @param  mix  $vendorIdOrUid
     * @return array
     */
    public function fetchItVendor($vendorIdOrUid)
    {
        return $this->primaryModel::leftJoin('users', 'users.vendors__id', '=', 'vendors._id')
            ->select(
                __nestedKeyValues([
                    // 'vendors.*',
                    'vendors' => [
                        '_id',
                        '_uid',
                        'title',
                        'created_at',
                        'status as store_status',
                    ],
                    'users' => [
                        '_id as userId',
                        '_uid as userUId',
                        'username as username',
                        'email',
                        'status',
                        'first_name',
                        'last_name',
                        'mobile_number',
                        DB::raw('CONCAT(users.first_name, " ", users.last_name) AS FullName'),
                    ],
                ])
            )
            ->where('vendors._uid', $vendorIdOrUid)
            ->first()
            ->toArray();
    }

    /**
     * Update Account
     *
     * @param  object  $vendorData
     * @param  array  $requireColumnsForVendor
     * @return bool
     */
    public function updateAccountData($vendorData, $requireColumnsForVendor)
    {
        // Check if page updated then return positive response
        if ($vendorData->modelUpdate($requireColumnsForVendor)) {
            return true;
        }

        return false;
    }

    /**
     * Get latest 10 vendors
     *
     * @return object
     */
    public function newVendors()
    {
        return $this->primaryModel::limit(10)->latest()->get([
            '_uid',
            'title',
            'created_at',
            'slug',
            'status',
        ]);
    }

    /**
     * Get New Registrations counts of the vendors by default for last 12 months
     *
     * @param  date  $startDate
     * @param  date  $endDate
     * @return array
     */
    public function vendorRegistrationsStats($startDate = null, $endDate = null)
    {
        if (! $startDate) {
            $startDate = Carbon::now()->subMonth(11)->firstOfMonth();
        }

        if (! $endDate) {
            $endDate = Carbon::now()->lastOfMonth();
        }

        $allTheMonths = collect(range(11, 0))->map(function ($i) {
            $dt = today()->startOfMonth()->subMonth($i);

            return [
                'vendors_count' => 0,
                'month_name' => $dt->translatedFormat('M').' '.__tr($dt->translatedFormat('Y')),
                'month' => $dt->shortMonthName,
                'year' => $dt->year,
                'month_number' => $dt->month,
                'month_year' => $dt->month.'-'.$dt->year,
            ];
        });

        $dataRecords = $this->primaryModel::select(
            DB::raw("COUNT(*) AS vendors_count, DATE_FORMAT(created_at, '%c-%Y') month_year"),
            DB::raw("DATE_FORMAT(created_at, '%Y') year"),
            DB::raw('MONTH(created_at) month_number'),
            // DB::raw("CONCAT(DATE_FORMAT(created_at, ' %b'), DATE_FORMAT(created_at, ' %Y')) as month_name")
        )
            ->whereBetween('vendors.created_at', [
                $startDate,
                $endDate,
            ])
            ->groupBy('month_year', 'year', 'month_number')
            ->orderBy('year')
            ->orderBy('month_number')
            ->get();

        return arrayExtend(
            __reIndexArray($allTheMonths->toArray(), 'month_year'),
            __reIndexArray($dataRecords->toArray(), 'month_year')
        );
    }
}
