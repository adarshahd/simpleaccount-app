<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleItemStoreRequest;
use App\Http\Requests\SaleItemUpdateRequest;
use App\Http\Resources\SaleItemCollection;
use App\Http\Resources\SaleItemResource;
use App\Models\Sale;
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
        $items = SaleItem::all();

        return new SaleItemCollection($items);
    }

    /**
     * @param \App\Http\Requests\SaleItemStoreRequest $request
     * @return \App\Http\Resources\SaleItemResource
     */
    public function store(SaleItemStoreRequest $request)
    {
        $item = SaleItem::create($request->validated());

        return new SaleItemResource($item);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SaleItem $item
     * @return \App\Http\Resources\SaleItemResource
     */
    public function show(Request $request, Sale $sale, SaleItem $item)
    {
        return new SaleItemResource($item);
    }

    /**
     * @param \App\Http\Requests\SaleItemUpdateRequest $request
     * @param \App\Models\SaleItem $item
     * @return \App\Http\Resources\SaleItemResource
     */
    public function update(SaleItemUpdateRequest $request, Sale $sale, SaleItem $item)
    {
        $item->update($request->validated());

        return new SaleItemResource($item);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SaleItem $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Sale $sale, SaleItem $item)
    {
        $item->delete();

        return response()->noContent();
    }
}
