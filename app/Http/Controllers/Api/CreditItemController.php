<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditItemStoreRequest;
use App\Http\Requests\CreditItemUpdateRequest;
use App\Http\Resources\CreditItemCollection;
use App\Http\Resources\CreditItemResource;
use App\Models\CreditItem;
use Illuminate\Http\Request;

class CreditItemController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\CreditItemCollection
     */
    public function index(Request $request)
    {
        $creditItems = CreditItem::all();

        return new CreditItemCollection($creditItems);
    }

    /**
     * @param \App\Http\Requests\CreditItemStoreRequest $request
     * @return \App\Http\Resources\CreditItemResource
     */
    public function store(CreditItemStoreRequest $request)
    {
        $creditItem = CreditItem::create($request->validated());

        return new CreditItemResource($creditItem);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CreditItem $creditItem
     * @return \App\Http\Resources\CreditItemResource
     */
    public function show(Request $request, CreditItem $creditItem)
    {
        return new CreditItemResource($creditItem);
    }

    /**
     * @param \App\Http\Requests\CreditItemUpdateRequest $request
     * @param \App\Models\CreditItem $creditItem
     * @return \App\Http\Resources\CreditItemResource
     */
    public function update(CreditItemUpdateRequest $request, CreditItem $creditItem)
    {
        $creditItem->update($request->validated());

        return new CreditItemResource($creditItem);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CreditItem $creditItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, CreditItem $creditItem)
    {
        $creditItem->delete();

        return response()->noContent();
    }
}
