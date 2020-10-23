<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DebitItemStoreRequest;
use App\Http\Requests\DebitItemUpdateRequest;
use App\Http\Resources\DebitItemCollection;
use App\Http\Resources\DebitItemResource;
use App\Models\DebitItem;
use Illuminate\Http\Request;

class DebitItemController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\DebitItemCollection
     */
    public function index(Request $request)
    {
        $debitItems = DebitItem::all();

        return new DebitItemCollection($debitItems);
    }

    /**
     * @param \App\Http\Requests\DebitItemStoreRequest $request
     * @return \App\Http\Resources\DebitItemResource
     */
    public function store(DebitItemStoreRequest $request)
    {
        $debitItem = DebitItem::create($request->validated());

        return new DebitItemResource($debitItem);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DebitItem $debitItem
     * @return \App\Http\Resources\DebitItemResource
     */
    public function show(Request $request, DebitItem $debitItem)
    {
        return new DebitItemResource($debitItem);
    }

    /**
     * @param \App\Http\Requests\DebitItemUpdateRequest $request
     * @param \App\Models\DebitItem $debitItem
     * @return \App\Http\Resources\DebitItemResource
     */
    public function update(DebitItemUpdateRequest $request, DebitItem $debitItem)
    {
        $debitItem->update($request->validated());

        return new DebitItemResource($debitItem);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DebitItem $debitItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, DebitItem $debitItem)
    {
        $debitItem->delete();

        return response()->noContent();
    }
}
