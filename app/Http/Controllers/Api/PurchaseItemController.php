<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseItemStoreRequest;
use App\Http\Requests\PurchaseItemUpdateRequest;
use App\Http\Resources\PurchaseItemCollection;
use App\Http\Resources\PurchaseItemResource;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\PurchaseItemCollection
     */
    public function index(Request $request)
    {
        $purchaseItems = PurchaseItem::all();

        return new PurchaseItemCollection($purchaseItems);
    }

    /**
     * @param \App\Http\Requests\PurchaseItemStoreRequest $request
     * @return \App\Http\Resources\PurchaseItemResource
     */
    public function store(PurchaseItemStoreRequest $request)
    {
        $purchaseItem = PurchaseItem::create($request->validated());

        return new PurchaseItemResource($purchaseItem);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PurchaseItem $purchaseItem
     * @return \App\Http\Resources\PurchaseItemResource
     */
    public function show(Request $request, Purchase $purchase, PurchaseItem $purchaseItem)
    {
        return new PurchaseItemResource($purchaseItem);
    }

    /**
     * @param \App\Http\Requests\PurchaseItemUpdateRequest $request
     * @param \App\Models\PurchaseItem $purchaseItem
     * @return \App\Http\Resources\PurchaseItemResource
     */
    public function update(PurchaseItemUpdateRequest $request, Purchase $purchase, PurchaseItem $purchaseItem)
    {
        $purchaseItem->update($request->validated());

        return new PurchaseItemResource($purchaseItem);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PurchaseItem $purchaseItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Purchase $purchase, PurchaseItem $purchaseItem)
    {
        $purchaseItem->delete();

        return response()->noContent();
    }
}
