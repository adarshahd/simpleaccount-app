<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PurchaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseStoreRequest;
use App\Http\Requests\PurchaseUpdateRequest;
use App\Http\Resources\PurchaseCollection;
use App\Http\Resources\PurchaseResource;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\PurchaseCollection
     */
    public function index(Request $request)
    {
        $purchases = Purchase::all();

        return new PurchaseCollection($purchases);
    }

    /**
     * @param \App\Http\Requests\PurchaseStoreRequest $request
     * @return PurchaseResource|\Illuminate\Http\JsonResponse
     */
    public function store(PurchaseStoreRequest $request)
    {
        $purchaseItems = $request->input('purchase_items');

        try {
            //Create purchase and purchase item entries inside a transaction
            DB::beginTransaction();

            $purchase = Purchase::query()->create([
                'bill_number' => $request->get('bill_number'),
                'bill_date' => $request->get('bill_date'),
                'vendor_id' => $request->get('vendor_id'),
                'sub_total' => 0,
                'discount' => 0,
                'tax' => 0,
                'total' => 0,
            ]);

            $purchaseAddResult = $this->addPurchaseItems($purchase, $purchaseItems);

            $subTotal = $purchaseAddResult['sub_total'];
            $tax = $purchaseAddResult['tax'];

            $discount = $request->get('discount');
            $total = ($subTotal + $tax) - $discount;

            $purchase->update([
                'sub_total' => $subTotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
            ]);

            DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();

            return response()->json([
                'message' => "There were some error processing your request. Please check and try again.",
                'description' => $e->getMessage()
            ], 422);
        }

        return new PurchaseResource($purchase->refresh());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Purchase $purchase
     * @return \App\Http\Resources\PurchaseResource
     */
    public function show(Request $request, Purchase $purchase)
    {
        return new PurchaseResource($purchase);
    }

    /**
     * @param \App\Http\Requests\PurchaseUpdateRequest $request
     * @param \App\Models\Purchase $purchase
     * @return PurchaseResource|\Illuminate\Http\JsonResponse
     */
    public function update(PurchaseUpdateRequest $request, Purchase $purchase)
    {
        $purchaseItems = $request->input('purchase_items');

        try {
            //Start transaction
            DB::beginTransaction();

            $purchasedItems = $purchase->purchaseItems()->get();
            foreach($purchasedItems as $purchasedItem) {
                $oldQuantity = $purchasedItem->quantity;
                $currentStock = $purchasedItem->productStock;
                $quantityTobeAdded = 0;

                $purchaseItemCollection = collect($purchaseItems);
                $purchaseItem = $purchaseItemCollection
                    ->where('batch', $purchasedItem->batch)
                    ->where('hsn', $purchasedItem->hsn)
                    ->where('expiry', $purchasedItem->expiry)
                    ->where('price', $purchasedItem->price)
                    ->where('mrp', $purchasedItem->mrp)
                    ->where('manufacturer_id', $purchasedItem->manufacturer_id)
                    ->first();

                if($purchaseItem != null) {
                    $quantityTobeAdded += $purchaseItem['quantity'];
                }

                if($oldQuantity > ($currentStock->stock + $quantityTobeAdded)) {
                    //There is not enough stock to update this item.
                    $productName = Product::query()->find($purchasedItem->product_id)->name;
                    throw new PurchaseException("There is not enough stock of $productName. Modification can not be done!");
                }

                $currentStock->update([
                    'stock' => $currentStock->stock - $oldQuantity,
                    'total_stock' => $currentStock->total_stock - $oldQuantity
                ]);

                $purchasedItem->delete();
            }

            $purchaseAddResult = $this->addPurchaseItems($purchase, $purchaseItems);
            $subTotal = $purchaseAddResult['sub_total'];
            $tax = $purchaseAddResult['tax'];

            $discount = $request->get('discount');
            $total = ($subTotal + $tax) - $discount;

            $purchase->update([
                'bill_number' => $request->get('bill_number'),
                'bill_date' => $request->get('bill_date'),
                'vendor_id' => $request->get('vendor_id'),
                'sub_total' => $subTotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
            ]);

            //commit transaction
            DB::commit();

            return new PurchaseResource($purchase->refresh());
        } catch (PurchaseException $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'description' => $e->getMessage()
            ], 422);
        } catch (\PDOException $e) {
            DB::rollBack();

            return response()->json([
                'message' => "There were some error processing your request. Please check and try again.",
                'description' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Purchase $purchase
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Purchase $purchase)
    {
        return response()->json([
            'status' => false,
            'error' => 401,
            'error_message' => "Can not delete this Purchase!"
        ]);
        /*$purchase->delete();

        return response()->noContent();*/
    }

    private function addPurchaseItems($purchase, $purchaseItems) {
        $subTotal = 0;
        $tax = 0;

        foreach($purchaseItems as $purchaseItem) {
            $product = Product::query()->find($purchaseItem['product_id']);
            $discount = $purchaseItem['discount'];
            $price = $purchaseItem['price'] - $discount;
            $purchaseItemPrice = ($price * $purchaseItem['quantity']);
            $subTotal = $subTotal + $purchaseItemPrice;
            $purchaseItem['tax'] = $purchaseItemPrice * ($product->tax->tax / 100);
            $tax = $tax + $purchaseItem['tax'];

            $stockId = $this->updateStock($product, $purchaseItem);

            PurchaseItem::query()->create([
                'quantity' => $purchaseItem['quantity'],
                'discount' => $purchaseItem['discount'],
                'purchase_id' => $purchase->id,
                'product_stock_id' => $stockId,
            ]);
        }

        return [
            'sub_total' => $subTotal,
            'tax' => $tax
        ];
    }

    private function updateStock($product, $purchaseItem) {
        $productStock = $product->productStocks()
            ->where('hsn', $purchaseItem['hsn'] == null ? '' : $purchaseItem['hsn'])
            ->where('batch', $purchaseItem['batch'])
            ->whereDate('expiry', Carbon::parse($purchaseItem['expiry']))
            ->where('price', $purchaseItem['price'])
            ->where('mrp', $purchaseItem['mrp'])
            ->where('manufacturer_id', $purchaseItem['manufacturer_id'])
            ->first();

        if($productStock != null) {
            $currentStock = $productStock->stock;
            $totalStock = $productStock->total_stock;

            $productStock->update([
                'stock' => $currentStock + $purchaseItem['quantity'],
                'total_stock' => $totalStock + $purchaseItem['quantity']
            ]);
        } else {
            $productStock = ProductStock::query()->create([
                'identification' => '',
                'stock' => $purchaseItem['quantity'],
                'total_stock' => $purchaseItem['quantity'],
                'mrp' => $purchaseItem['mrp'],
                'price' => $purchaseItem['price'],
                'tax' => $purchaseItem['tax'] / $purchaseItem['quantity'], // Tax is recorded for <quantity> items
                'batch' => $purchaseItem['batch'],
                'hsn' => $purchaseItem['hsn'] == null ? '' : $purchaseItem['hsn'],
                'expiry' => $purchaseItem['expiry'],
                'product_id' => $purchaseItem['product_id'],
                'manufacturer_id' => $purchaseItem['manufacturer_id'],
            ]);
        }

        return $productStock->id;
    }
}
