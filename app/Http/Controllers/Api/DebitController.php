<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DebitStoreRequest;
use App\Http\Requests\DebitUpdateRequest;
use App\Http\Resources\DebitCollection;
use App\Http\Resources\DebitResource;
use App\Models\Debit;
use Illuminate\Http\Request;

class DebitController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\DebitCollection
     */
    public function index(Request $request)
    {
        $debits = Debit::all();

        return new DebitCollection($debits);
    }

    /**
     * @param \App\Http\Requests\DebitStoreRequest $request
     * @return \App\Http\Resources\DebitResource
     */
    public function store(DebitStoreRequest $request)
    {
        $debit = Debit::create($request->validated());

        return new DebitResource($debit);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Debit $debit
     * @return \App\Http\Resources\DebitResource
     */
    public function show(Request $request, Debit $debit)
    {
        return new DebitResource($debit);
    }

    /**
     * @param \App\Http\Requests\DebitUpdateRequest $request
     * @param \App\Models\Debit $debit
     * @return \App\Http\Resources\DebitResource
     */
    public function update(DebitUpdateRequest $request, Debit $debit)
    {
        $debit->update($request->validated());

        return new DebitResource($debit);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Debit $debit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Debit $debit)
    {
        $debit->delete();

        return response()->noContent();
    }
}
