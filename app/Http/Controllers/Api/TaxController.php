<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxStoreRequest;
use App\Http\Requests\TaxUpdateRequest;
use App\Http\Resources\TaxCollection;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\TaxCollection
     */
    public function index(Request $request)
    {
        $taxes = Tax::query()->latest()->paginate();

        return new TaxCollection($taxes);
    }

    /**
     * @param \App\Http\Requests\TaxStoreRequest $request
     * @return \App\Http\Resources\TaxResource
     */
    public function store(TaxStoreRequest $request)
    {
        $tax = Tax::create($request->validated());

        return new TaxResource($tax);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Tax $tax
     * @return \App\Http\Resources\TaxResource
     */
    public function show(Request $request, Tax $tax)
    {
        return new TaxResource($tax);
    }

    /**
     * @param \App\Http\Requests\TaxUpdateRequest $request
     * @param \App\Models\Tax $tax
     * @return \App\Http\Resources\TaxResource
     */
    public function update(TaxUpdateRequest $request, Tax $tax)
    {
        $tax->update($request->validated());

        return new TaxResource($tax);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Tax $tax
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Tax $tax)
    {
        $tax->delete();

        return response()->noContent();
    }
}
