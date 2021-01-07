<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppSettingStoreRequest;
use App\Http\Requests\AppSettingUpdateRequest;
use App\Http\Requests\ProductOwnerRequest;
use App\Http\Requests\RegionRequest;
use App\Http\Resources\AppSettingCollection;
use App\Http\Resources\AppSettingResource;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use PragmaRX\Countries\Package\Countries;
use Symfony\Component\HttpFoundation\Response;

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

    private static $voucherBillPrefix = "voucher_bill_prefix";
    private static $voucherBillStart = "voucher_bill_start";
    private static $voucherBillFooter = "voucher_bill_footer";

    private static $appInitialized = "app_initialized";
    private static $poData = 'po_data';
    private static $roleAndPermissionSeeded = 'roles_permissions_seeded';

    private static $regionData = 'region';

    // Support little customer data
    private static $allowSimpleCustomerVendorData = 'allow_simple_customer_vendor_data';

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

    public function find(Request $request) {
        $setting = AppSetting::query()->where('key', $request->input('key'))->first();
        if($setting) {
            return new AppSettingResource($setting);
        } else {
            return response()->json([
                'data' => []
            ]);
        }
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
        if(!$productOwnerSetting) {
            return false;
        }
        $productOwnerData = json_decode($productOwnerSetting->value);
        if(!$productOwnerData) {
            return false;
        }
        if($productOwnerData->name == '' || $productOwnerData->contact_name == '') {
            return false;
        } else {
            return true;
        }
    }

    public static function isRegionUpdated() {
        $regionSettings = AppSetting::query()->where('key', self::$regionData)->first();
        if(!$regionSettings) {
            return false;
        }

        $regionData = json_decode($regionSettings);
        if(!$regionData) {
            return false;
        }
        if($regionData->country == '' || $regionData->currency == '') {
            return false;
        } else {
            return true;
        }
    }

    public static function getProductOwnerData() {
        $productOwnerSetting = AppSetting::query()->where('key', self::$poData)->first();
        if(!$productOwnerSetting) {
            $productOwnerSetting = AppSetting::query()->create([
                'key' => self::$poData,
                'value' => '{"name" : "","identification" : "","address_line_1" : "","address_line_2" : "","city" : "","state" : "","country" : "","pin" : "","contact_name" : "","contact_email" : "","contact_phone" : "","website" : "","id_type_id" : "","logo" : ""}'
            ]);
        }
        return json_decode($productOwnerSetting->value);
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

    public static function getVoucherBillPrefix() {
        $billPrefixSetting = AppSetting::query()->where('key', self::$voucherBillPrefix)->first();
        return $billPrefixSetting ? $billPrefixSetting->value : '';
    }

    public static function getVoucherBillStart() {
        $billStartSetting = AppSetting::query()->where('key', self::$voucherBillStart)->first();
        return $billStartSetting ? $billStartSetting->value : 1;
    }

    public static function getVoucherBillFooter() {
        $creditBillFooterSetting = AppSetting::query()->where('key', self::$voucherBillFooter)->first();
        return $creditBillFooterSetting ? $creditBillFooterSetting->value : '';
    }

    public static function shouldAllowSimpleCustomerVendorData() {
        $setting = AppSetting::query()->where('key', self::$allowSimpleCustomerVendorData)->first();
        return $setting ? $setting->value : false;
    }

    public function getApplicationSettings(Request $request) {
        $productOwnerData = AppSetting::query()->where('key', self::$poData)->first();
        if(!$productOwnerData) {
            $productOwnerData = AppSetting::query()->create([
                'key' => self::$poData,
                'value' => '{"name" : "SimpleAccount","identification" : "","address_line_1" : "","address_line_2" : "","city" : "","state" : "","country" : "","pin" : "","contact_name" : "","contact_email" : "","contact_phone" : "","website" : "","id_type_id" : null,"logo" : ""}'
            ]);
        }

        $regionSettings = AppSetting::query()->where('key', self::$regionData)->first();
        if(!$regionSettings) {
            $regionSettings = AppSetting::query()->create([
                'key' => self::$regionData,
                'value' => '{"country" : "India", "currency" : "INR", "symbol" : "₹"}'
            ]);
        }

        return response()->json([
            'data' => [
                'product_owner_data' => json_decode($productOwnerData->value),
                'regional_settings_data' => json_decode($regionSettings->value)
            ]
        ]);
    }

    public function updateProductData(ProductOwnerRequest $request) {
        $productOwnerData = $request->validated();
        if($request->hasFile('logo')) {
            $request->file('logo')->storeAs('public/logos', 'logo.png');
        }
        $productOwnerData['logo'] = asset('storage/logos/logo.png');

        $setting = AppSetting::query()->where('key', self::$poData)->first();
        if($setting) {
            $setting->update([
                'value' => json_encode($productOwnerData)
            ]);
        } else {
            $setting = AppSetting::query()->create([
                'key' => self::$poData,
                'value' => json_encode($productOwnerData)
            ]);
        }

        return response()->json([
            'data' => [
                'status' => true
            ]
        ]);
    }

    public function getProductData(Request $request) {
        $productData = self::getProductOwnerData();
        return response()->json([
            'data' => $productData
        ]);
    }

    public function updateRegionData(RegionRequest $request) {
        $currency = Countries::where('name.common', $request->input('country'))->first()->hydrate('currencies')->currencies->first();
        $currencyName = $currency->name;
        $currencySymbol = $currency->units->major->symbol;
        $regionData = new \stdClass();
        $regionData->country = $request->input('country');
        $regionData->currency_name = $currencyName;
        $regionData->currency_symbol = $currencySymbol;

        $setting = AppSetting::query()->where('key', self::$regionData)->first();
        if($setting) {
            $setting->update([
                'value' => json_encode($regionData)
            ]);
        } else {
            $setting = AppSetting::query()->create([
                'key' => self::$regionData,
                'value' => json_encode($regionData)
            ]);
        }

        self::setApplicationInitialized();

        return response()->json([
            'data' => [
                'status' => true
            ]
        ]);
    }
}
