<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppSettingStoreRequest;
use App\Http\Requests\AppSettingUpdateRequest;
use App\Http\Resources\AppSettingCollection;
use App\Http\Resources\AppSettingResource;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    /*
     *  Constants required to store known settings
     * */

    private static $requireExpiryDate = "require_expiry";
    private static $saleBillPrefixSetting = "sale_bill_prefix";
    private static $saleBillStart = "sale_bill_start";
    private static $saleBillFooter = "sale_bill_footer";

    private static $creditBillPrefix = "credit_bill_prefix";
    private static $creditBillStart = "credit_bill_start";
    private static $creditBillFooter = "credit_bill_footer";

    private static $debitBillPrefix = "debit_bill_prefix";
    private static $debitBillStart = "debit_bill_start";
    private static $debitBillFooter = "debit_bill_footer";

    private static $receiptBillPrefix = "receipt_bill_prefix";
    private static $receiptBillStart = "receipt_bill_start";
    private static $receiptBillFooter = "receipt_bill_footer";

    private static $appInitialized = "app_initialized";
    private static $poData = 'po_data';
    private static $roleAndPermissionSeeded = 'roles_permissions_seeded';

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\AppSettingCollection
     */
    public function index(Request $request)
    {
        $settings = AppSetting::all();

        return new AppSettingCollection($settings);
    }

    /**
     * @param \App\Http\Requests\AppSettingStoreRequest $request
     * @return \App\Http\Resources\AppSettingResource
     */
    public function store(AppSettingStoreRequest $request)
    {
        $setting = AppSetting::query()->create($request->validated());

        return new AppSettingResource($setting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AppSetting $setting
     * @return \App\Http\Resources\AppSettingResource
     */
    public function show(Request $request, AppSetting $setting)
    {
        return new AppSettingResource($setting);
    }

    /**
     * @param \App\Http\Requests\AppSettingUpdateRequest $request
     * @param \App\Models\AppSetting $setting
     * @return \App\Http\Resources\AppSettingResource
     */
    public function update(AppSettingUpdateRequest $request, AppSetting $setting)
    {
        $setting->update($request->validated());

        return new AppSettingResource($setting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AppSetting $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, AppSetting $setting)
    {
        $setting->delete();

        return response()->noContent();
    }

    public static function isApplicationInitialized() {
        $appInitializedSetting = AppSetting::query()->where('key', self::$appInitialized)->first();
        return $appInitializedSetting ? $appInitializedSetting->value : false;
    }

    public static function setApplicationInitialized() {
        AppSetting::query()->where('key', self::$appInitialized)->firstOrCreate([
            'key' => self::$appInitialized,
            'value' => true
        ]);
    }

    public static function isProductOwnerUpdated() {
        $productOwnerSetting = AppSetting::query()->where('key', self::$poData)->first();
        return $productOwnerSetting != null;
    }

    public static function getProductOwnerData() {
        $productOwnerSetting = AppSetting::query()->where('key', self::$poData)->first();
        return json_decode($productOwnerSetting->value);
    }

    public static function updateProductOwnerData($data) {
        $productOwnerData = AppSetting::query()->where('key', self::$poData)->first();
        if($productOwnerData) {
            $productOwnerData->update([
                'value' => $data
            ]);
        } else {
            $productOwnerData = AppSetting::query()->create([
                'key' => self::$poData,
                'value' => $data
            ]);
        }

        return $productOwnerData != null;
    }

    public static function isRolesAndPermissionsSeeded()
    {
        $setting = AppSetting::query()->where('key', self::$roleAndPermissionSeeded)->first();
        return $setting ? $setting->value : false;
    }

    public static function setRolesAndPermissionsSeeded() {
        AppSetting::query()->where('key', self::$roleAndPermissionSeeded)->firstOrCreate([
            'key' => self::$roleAndPermissionSeeded,
            'value' => true
        ]);
    }

    public static function getRequireExpiry() {
        $requireExpirySetting = AppSetting::query()->where('key', self::$requireExpiryDate)->first();
        return $requireExpirySetting ? $requireExpirySetting->value : true;
    }

    public static function getSaleBillPrefix()
    {
        $billPrefixSetting = AppSetting::query()->where('key', self::$saleBillPrefixSetting)->first();
        return $billPrefixSetting ? $billPrefixSetting->value : '';
    }

    public static function getSaleBillStart() {
        $billStartSetting = AppSetting::query()->where('key', self::$saleBillStart)->first();
        return $billStartSetting ? $billStartSetting->value : 1;
    }

    public static function getSaleBillFooter() {
        $saleBillFooterSetting = AppSetting::query()->where('key', self::$saleBillFooter)->first();
        return $saleBillFooterSetting ? $saleBillFooterSetting->value : '';
    }

    public static function getCreditBillPrefix() {
        $billPrefixSetting = AppSetting::query()->where('key', self::$creditBillPrefix)->first();
        return $billPrefixSetting ? $billPrefixSetting->value : '';
    }

    public static function getCreditBillStart() {
        $billStartSetting = AppSetting::query()->where('key', self::$creditBillStart)->first();
        return $billStartSetting ? $billStartSetting->value : 1;
    }

    public static function getCreditBillFooter() {
        $creditBillFooterSetting = AppSetting::query()->where('key', self::$creditBillFooter)->first();
        return $creditBillFooterSetting ? $creditBillFooterSetting->value : '';
    }

    public static function getDebitBillPrefix() {
        $billPrefixSetting = AppSetting::query()->where('key', self::$debitBillPrefix)->first();
        return $billPrefixSetting ? $billPrefixSetting->value : '';
    }

    public static function getDebitBillStart() {
        $billStartSetting = AppSetting::query()->where('key', self::$debitBillStart)->first();
        return $billStartSetting ? $billStartSetting->value : 1;
    }

    public static function getDebitBillFooter() {
        $creditBillFooterSetting = AppSetting::query()->where('key', self::$debitBillFooter)->first();
        return $creditBillFooterSetting ? $creditBillFooterSetting->value : '';
    }

    public static function getReceiptBillPrefix() {
        $billPrefixSetting = AppSetting::query()->where('key', self::$receiptBillPrefix)->first();
        return $billPrefixSetting ? $billPrefixSetting->value : '';
    }

    public static function getReceiptBillStart() {
        $billStartSetting = AppSetting::query()->where('key', self::$receiptBillStart)->first();
        return $billStartSetting ? $billStartSetting->value : 1;
    }

    public static function getReceiptBillFooter() {
        $creditBillFooterSetting = AppSetting::query()->where('key', self::$receiptBillFooter)->first();
        return $creditBillFooterSetting ? $creditBillFooterSetting->value : '';
    }
}
