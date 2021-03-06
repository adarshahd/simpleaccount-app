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
        $purchaseQuery = Purchase::query();
        if($request->has('vendor_id')) {
            $purchaseQuery = $purchaseQuery->where('vendor_id', $request->input('vendor_id'));
        }

        $purchases = $purchaseQuery->latest()->paginate();

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
                'sub_total' => round($subTotal, 2),
                'discount' => $discount,
                'tax' => round($tax, 2),
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
                    ->where('batch', $currentStock->batch)
                    ->where('expiry', $currentStock->expiry->format('Y-m-d'))
                    ->where('price', ($currentStock->price + $currentStock->tax))
                    ->where('mrp', $currentStock->mrp)
                    ->where('manufacturer_id', $currentStock->manufacturer_id)
                    ->first();

                if($purchaseItem != null) {
                    $quantityTobeAdded += $purchaseItem['quantity'];
                }

                if($oldQuantity > ($currentStock->stock + $quantityTobeAdded)) {
                    //There is not enough stock to update this item.
                    $productName = Product::query()->find($purchasedItem->productStock->product->id)->name;
                    throw new PurchaseException("There is not enough stock of $productName. Modification can not be done!");
                }

                $currentStock->update([
                    'stock' => $currentStock->stock - $oldQuantity,
                    'total_stock' => $currentStock->total_stock - $oldQuantity
                ]);

                $currentStock->refresh();
                if($currentStock->stock == 0 && $currentStock->total_stock == 0) {
                    $currentStock->delete();
                }

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
                'sub_total' => round($subTotal, 2),
                'discount' => $discount,
                'tax' => round($tax, 2),
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
            $taxPercent = $product->tax->tax;
            $taxExcludedPrice = ($price / (( $taxPercent / 100 ) + 1 ));
            $purchaseItemPrice = ($taxExcludedPrice * $purchaseItem['quantity']);
            $purchaseItem['price'] = $taxExcludedPrice;
            $purchaseItem['tax_percent'] = $taxPercent;
            $purchaseItem['tax'] = $purchaseItemPrice * ($taxPercent / 100);
            $purchaseItem['hsn'] = $product->hsn;
            $tax = $tax + $purchaseItem['tax'];
            $subTotal = $subTotal + $purchaseItemPrice;

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
            ->where('batch', $purchaseItem['batch'])
            ->whereDate('expiry', Carbon::parse($purchaseItem['expiry']))
            ->where('price', round($purchaseItem['price'], 2))
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
                'tax_percent' => $purchaseItem['tax_percent'],
                'tax' => $purchaseItem['tax'] / $purchaseItem['quantity'], // Tax is recorded for <quantity> items
                'batch' => $purchaseItem['batch'],
                'expiry' => $purchaseItem['expiry'],
                'product_id' => $purchaseItem['product_id'],
                'manufacturer_id' => $purchaseItem['manufacturer_id'],
            ]);
        }

        return $productStock->id;
    }
}
