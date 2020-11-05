<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1'], function() {
    /*
     * On Boarding routes
     * */
    Route::get('/status', [\App\Http\Controllers\OnboardController::class, 'getProductStatus']);
    Route::post('/users/admin', [\App\Http\Controllers\OnboardController::class, 'createAdminUser']);
    Route::post('/product-owner', [\App\Http\Controllers\OnboardController::class, 'updateProductOwnerData']);

    /*
     * Route to get current logged in user
     * */
    Route::group(['middleware' => 'auth:sanctum'], function() {

        Route::get('/user', function(Request $request) {
            return response()->json([
                'data' => [
                    'user' => $request->user()
                ]
            ]);
        });

    });

    /*
     * Extra routes
     * */

    Route::get('/customers/{customer}/details', [\App\Http\Controllers\Api\CustomerController::class, 'details']);
    Route::get('/vendors/{vendor}/details', [\App\Http\Controllers\Api\VendorController::class, 'details']);

    /*
     * API Resource Routes
     * */
    Route::apiResources([
        'settings' => App\Http\Controllers\Api\AppSettingController::class,
        'id-types' => App\Http\Controllers\Api\IdTypeController::class,
        'users' => App\Http\Controllers\Api\UserController::class,
        'customers' => App\Http\Controllers\Api\CustomerController::class,
        'vendors' => App\Http\Controllers\Api\VendorController::class,
        'manufacturers' => App\Http\Controllers\Api\ManufacturerController::class,
        'taxes' => App\Http\Controllers\Api\TaxController::class,
        'product-types' => App\Http\Controllers\Api\ProductTypeController::class,
        'products' => App\Http\Controllers\Api\ProductController::class,
        'products.stock' => App\Http\Controllers\Api\ProductStockController::class,
        'purchases' => App\Http\Controllers\Api\PurchaseController::class,
        'sales' => App\Http\Controllers\Api\SaleController::class,
        'debits' => App\Http\Controllers\Api\DebitController::class,
        'credits' => App\Http\Controllers\Api\CreditController::class,
        'receipts' => App\Http\Controllers\Api\ReceiptController::class,
        'vouchers' => App\Http\Controllers\Api\VoucherController::class,
        'incomes' => App\Http\Controllers\Api\IncomeController::class,
        'expenses' => App\Http\Controllers\Api\ExpenseController::class,
    ]);
});
