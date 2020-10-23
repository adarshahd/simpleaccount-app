<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleStoreRequest;
use App\Http\Requests\SaleUpdateRequest;
use App\Http\Resources\SaleCollection;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\SaleCollection
     */
    public function index(Request $request)
    {
        $sales = Sale::all();

        return new SaleCollection($sales);
    }

    /**
     * @param \App\Http\Requests\SaleStoreRequest $request
     * @return \App\Http\Resources\SaleResource
     */
    public function store(SaleStoreRequest $request)
    {
        $sale = Sale::create($request->validated());

        return new SaleResource($sale);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sale $sale
     * @return \App\Http\Resources\SaleResource
     */
    public function show(Request $request, Sale $sale)
    {
        return new SaleResource($sale);
    }

    /**
     * @param \App\Http\Requests\SaleUpdateRequest $request
     * @param \App\Models\Sale $sale
     * @return \App\Http\Resources\SaleResource
     */
    public function update(SaleUpdateRequest $request, Sale $sale)
    {
        $sale->update($request->validated());

        return new SaleResource($sale);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sale $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Sale $sale)
    {
        $sale->delete();

        return response()->noContent();
    }
}
