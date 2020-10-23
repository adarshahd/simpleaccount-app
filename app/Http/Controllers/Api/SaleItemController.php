<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleItemStoreRequest;
use App\Http\Requests\SaleItemUpdateRequest;
use App\Http\Resources\SaleItemCollection;
use App\Http\Resources\SaleItemResource;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class SaleItemController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\SaleItemCollection
     */
    public function index(Request $request)
    {
        $saleItems = SaleItem::all();

        return new SaleItemCollection($saleItems);
    }

    /**
     * @param \App\Http\Requests\SaleItemStoreRequest $request
     * @return \App\Http\Resources\SaleItemResource
     */
    public function store(SaleItemStoreRequest $request)
    {
        $saleItem = SaleItem::create($request->validated());

        return new SaleItemResource($saleItem);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SaleItem $saleItem
     * @return \App\Http\Resources\SaleItemResource
     */
    public function show(Request $request, SaleItem $saleItem)
    {
        return new SaleItemResource($saleItem);
    }

    /**
     * @param \App\Http\Requests\SaleItemUpdateRequest $request
     * @param \App\Models\SaleItem $saleItem
     * @return \App\Http\Resources\SaleItemResource
     */
    public function update(SaleItemUpdateRequest $request, SaleItem $saleItem)
    {
        $saleItem->update($request->validated());

        return new SaleItemResource($saleItem);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SaleItem $saleItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, SaleItem $saleItem)
    {
        $saleItem->delete();

        return response()->noContent();
    }
}
