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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('settings', App\Http\Controllers\Api\AppSettingController::class);

Route::apiResource('id-types', App\Http\Controllers\Api\IdTypeController::class);

Route::apiResource('users', App\Http\Controllers\Api\UserController::class);

Route::apiResource('customers', App\Http\Controllers\Api\CustomerController::class);

Route::apiResource('vendors', App\Http\Controllers\Api\VendorController::class);

Route::apiResource('manufacturers', App\Http\Controllers\Api\ManufacturerController::class);

Route::apiResource('taxes', App\Http\Controllers\Api\TaxController::class);

Route::apiResource('product-types', App\Http\Controllers\Api\ProductTypeController::class);

Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);

Route::apiResource('products.stock', App\Http\Controllers\Api\ProductStockController::class);

Route::apiResource('purchases', App\Http\Controllers\Api\PurchaseController::class);

Route::apiResource('purchases.items', App\Http\Controllers\Api\PurchaseItemController::class);

Route::apiResource('sales', App\Http\Controllers\Api\SaleController::class);

Route::apiResource('sales.items', App\Http\Controllers\Api\SaleItemController::class);

Route::apiResource('debits', App\Http\Controllers\Api\DebitController::class);

Route::apiResource('debits.items', App\Http\Controllers\Api\DebitItemController::class);

Route::apiResource('credits', App\Http\Controllers\Api\CreditController::class);

Route::apiResource('credits.items', App\Http\Controllers\Api\CreditItemController::class);

Route::apiResource('receipts', App\Http\Controllers\Api\ReceiptController::class);

Route::apiResource('vouchers', App\Http\Controllers\Api\VoucherController::class);

Route::apiResource('incomes', App\Http\Controllers\Api\IncomeController::class);

Route::apiResource('expenses', App\Http\Controllers\Api\ExpenseController::class);
