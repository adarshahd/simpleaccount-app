<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockStoreRequest;
use App\Http\Requests\ProductStockUpdateRequest;
use App\Http\Resources\ProductStockCollection;
use App\Http\Resources\ProductStockResource;
use App\Models\ProductStock;
use Illuminate\Http\Request;

class ProductStockController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ProductStockCollection
     */
    public function index(Request $request)
    {
        $productStocks = ProductStock::all();

        return new ProductStockCollection($productStocks);
    }

    /**
     * @param \App\Http\Requests\ProductStockStoreRequest $request
     * @return \App\Http\Resources\ProductStockResource
     */
    public function store(ProductStockStoreRequest $request)
    {
        $productStock = ProductStock::create($request->validated());

        return new ProductStockResource($productStock);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProductStock $productStock
     * @return \App\Http\Resources\ProductStockResource
     */
    public function show(Request $request, ProductStock $productStock)
    {
        return new ProductStockResource($productStock);
    }

    /**
     * @param \App\Http\Requests\ProductStockUpdateRequest $request
     * @param \App\Models\ProductStock $productStock
     * @return \App\Http\Resources\ProductStockResource
     */
    public function update(ProductStockUpdateRequest $request, ProductStock $productStock)
    {
        $productStock->update($request->validated());

        return new ProductStockResource($productStock);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProductStock $productStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ProductStock $productStock)
    {
        $productStock->delete();

        return response()->noContent();
    }
}
