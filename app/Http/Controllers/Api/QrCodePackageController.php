<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QrCodePackageController extends BaseTableDataController
{
    protected $model = \App\Models\QrCodePackage::class;
    protected $columns = [
        'sm_qrcode_packages.id',
        'sm_qrcode_packages.name',
        'sm_qrcode_packages.product_id',
        'sm_qrcode_packages.created_at',
        'sm_qrcode_packages.updated_at',
        'sm_qrcode_packages.stamp_type',
        'sm_qrcode_packages.serial_number_start',
        'sm_qrcode_packages.serial_number_end',
        'sm_qrcode_packages.product_package_id',
        'sm_qrcode_packages.qrcode_package_status_id',
        'sm_qrcode_packages.level_package',
        'sm_enterprises.name as enterprise_name',
        'sm_enterprises.district_code',
        'sm_enterprises.ward_code',
        'sm_qrcode_package_status.name as status_status',
        'user_create_by.username as create_by_name',
        'user_last_edit_by.username as last_edit_by_name',
        'sm_package_to_orders.name as product_package_name',
    ];
    protected $searchFields = ['sm_qrcode_packages.name', 'sm_enterprises.name'];
    protected $joins = [
        ['table' => 'sm_enterprises', 'first' => 'sm_enterprises.id', 'operator' => '=', 'second' => 'sm_qrcode_packages.enterprise_id'],
        ['table' => 'sm_qrcode_package_status', 'first' => 'sm_qrcode_package_status.id', 'operator' => '=', 'second' => 'sm_qrcode_packages.qrcode_package_status_id'],
        ['table' => 'users as user_create_by', 'first' => 'user_create_by.id', 'operator' => '=', 'second' => 'sm_qrcode_packages.create_by'],
        ['table' => 'users as user_last_edit_by', 'first' => 'user_last_edit_by.id', 'operator' => '=', 'second' => 'sm_qrcode_packages.last_edit_by'],
        ['table' => 'sm_package_to_orders', 'first' => 'sm_package_to_orders.id', 'operator' => '=', 'second' => 'sm_qrcode_packages.product_package_id'],
        ['table' => DB::raw('(SELECT qr_code_package_id, COUNT(DISTINCT serial_number) as totalstamp FROM sm_qr_codes GROUP BY qr_code_package_id) totalstamp'), 'first' => 'totalstamp.qr_code_package_id', 'operator' => '=', 'second' => 'sm_qrcode_packages.id'],
    ];
    protected $subQueries = [
        'product_name' => DB::raw("
            CASE
                WHEN sm_qrcode_packages.level_package = 'package'
                THEN (SELECT sm_products.name FROM sm_products JOIN sm_product_packages ON sm_product_packages.product_id = sm_products.id WHERE sm_qrcode_packages.product_package_id = sm_product_packages.id)
                WHEN sm_qrcode_packages.level_package = 'product'
                THEN (SELECT sm_products.name FROM sm_products WHERE sm_qrcode_packages.product_id = sm_products.id)
                ELSE '=='
            END as product_name
        "),
        'product_image' => DB::raw("
            CASE
                WHEN sm_qrcode_packages.level_package = 'package'
                THEN (SELECT sm_products.image FROM sm_products JOIN sm_product_packages ON sm_product_packages.product_id = sm_products.id WHERE sm_qrcode_packages.product_package_id = sm_product_packages.id)
                WHEN sm_qrcode_packages.level_package = 'product'
                THEN (SELECT sm_products.image FROM sm_products WHERE sm_qrcode_packages.product_id = sm_products.id)
                ELSE '=='
            END as product_image
        "),
        'product_real_id' => DB::raw("
            CASE
                WHEN sm_qrcode_packages.level_package = 'package'
                THEN (SELECT sm_products.id FROM sm_products JOIN sm_product_packages ON sm_product_packages.product_id = sm_products.id WHERE sm_qrcode_packages.product_package_id = sm_product_packages.id)
                WHEN sm_qrcode_packages.level_package = 'product'
                THEN (SELECT sm_products.id FROM sm_products WHERE sm_qrcode_packages.product_id = sm_products.id)
                ELSE '=='
            END as product_real_id
        "),
        'totalstamp' => DB::raw('totalstamp.totalstamp'),
    ];
    protected $customQueryCallback;
    protected $actionsCallback;

    public function __construct()
    {
        $this->customQueryCallback = function ($query) {
            $combinedSubQuery = DB::table('sm_qr_codes')
                ->select(
                    'qr_code_package_id',
                    DB::raw('MIN(serial_number) AS sm_qr_code_first'),
                    DB::raw('MAX(serial_number) AS sm_qr_code_last')
                )
                ->groupBy('qr_code_package_id');

            $cachedCombinedCodes = Cache::remember('sm_qr_codes_combined', now()->addMinutes(360), function () use ($combinedSubQuery) {
                return $combinedSubQuery->get();
            });

            $firstCodes = $cachedCombinedCodes->pluck('sm_qr_code_first', 'qr_code_package_id');
            $lastCodes = $cachedCombinedCodes->pluck('sm_qr_code_last', 'qr_code_package_id');

            $query->addSelect([
                DB::raw("IFNULL('{$firstCodes->toJson()}', NULL) AS sm_qr_code_first"),
                DB::raw("IFNULL('{$lastCodes->toJson()}', NULL) AS sm_qr_code_last"),
            ]);

            $query->where('sm_qrcode_packages.is_deleted', 0)
                  ->where('sm_enterprises.province_code', app('province_code'));
        };

        $this->actionsCallback = function ($item) {
            return sprintf(
                '<a href="%s" class="text-blue-500 hover:text-blue-700 mr-2" title="Edit">' .
                '<i class="fa-solid fa-pen-to-square"></i>' .
                '</a>' .
                '<button onclick="Livewire.dispatch(\'deleteUser\', [%d])" class="text-red-500 hover:text-red-700 mr-2" title="Delete">' .
                '<i class="fa-solid fa-trash"></i>' .
                '</button>' .
                '<button onclick="Livewire.dispatch(\'duplicatePackage\', [%d])" class="text-green-500 hover:text-green-700" title="Duplicate">' .
                '<i class="fa-solid fa-copy"></i>' .
                '</button>',
                route('qrcode_package.edit', $item->id),
                $item->id,
                $item->id
            );
        };
    }
}