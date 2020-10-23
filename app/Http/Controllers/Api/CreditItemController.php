<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditItemStoreRequest;
use App\Http\Requests\CreditItemUpdateRequest;
use App\Http\Resources\CreditItemCollection;
use App\Http\Resources\CreditItemResource;
use App\Models\Credit;
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
        $items = CreditItem::all();

        return new CreditItemCollection($items);
    }

    /**
     * @param \App\Http\Requests\CreditItemStoreRequest $request
     * @return \App\Http\Resources\CreditItemResource
     */
    public function store(CreditItemStoreRequest $request)
    {
        $item = CreditItem::create($request->validated());

        return new CreditItemResource($item);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CreditItem $item
     * @return \App\Http\Resources\CreditItemResource
     */
    public function show(Request $request, Credit $credit, CreditItem $item)
    {
        return new CreditItemResource($item);
    }

    /**
     * @param \App\Http\Requests\CreditItemUpdateRequest $request
     * @param \App\Models\CreditItem $item
     * @return \App\Http\Resources\CreditItemResource
     */
    public function update(CreditItemUpdateRequest $request, Credit $credit, CreditItem $item)
    {
        $item->update($request->validated());

        return new CreditItemResource($item);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CreditItem $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Credit $credit, CreditItem $item)
    {
        $item->delete();

        return response()->noContent();
    }
}
