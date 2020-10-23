<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DebitItemStoreRequest;
use App\Http\Requests\DebitItemUpdateRequest;
use App\Http\Resources\DebitItemCollection;
use App\Http\Resources\DebitItemResource;
use App\Models\Debit;
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
        $items = DebitItem::all();

        return new DebitItemCollection($items);
    }

    /**
     * @param \App\Http\Requests\DebitItemStoreRequest $request
     * @return \App\Http\Resources\DebitItemResource
     */
    public function store(DebitItemStoreRequest $request)
    {
        $item = DebitItem::create($request->validated());

        return new DebitItemResource($item);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DebitItem $item
     * @return \App\Http\Resources\DebitItemResource
     */
    public function show(Request $request, Debit $debit, DebitItem $item)
    {
        return new DebitItemResource($item);
    }

    /**
     * @param \App\Http\Requests\DebitItemUpdateRequest $request
     * @param \App\Models\DebitItem $item
     * @return \App\Http\Resources\DebitItemResource
     */
    public function update(DebitItemUpdateRequest $request, Debit $debit, DebitItem $item)
    {
        $item->update($request->validated());

        return new DebitItemResource($item);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DebitItem $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Debit $debit, DebitItem $item)
    {
        $item->delete();

        return response()->noContent();
    }
}
