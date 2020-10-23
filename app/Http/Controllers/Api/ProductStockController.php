<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockStoreRequest;
use App\Http\Requests\ProductStockUpdateRequest;
use App\Http\Resources\ProductStockCollection;
use App\Http\Resources\ProductStockResource;
use App\Models\Product;
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
        $stocks = ProductStock::all();

        return new ProductStockCollection($stocks);
    }

    /**
     * @param \App\Http\Requests\ProductStockStoreRequest $request
     * @return \App\Http\Resources\ProductStockResource
     */
    public function store(ProductStockStoreRequest $request)
    {
        $stock = ProductStock::create($request->validated());

        return new ProductStockResource($stock);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProductStock $stock
     * @return \App\Http\Resources\ProductStockResource
     */
    public function show(Request $request, Product $product, ProductStock $stock)
    {
        return new ProductStockResource($stock);
    }

    /**
     * @param \App\Http\Requests\ProductStockUpdateRequest $request
     * @param \App\Models\ProductStock $stock
     * @return \App\Http\Resources\ProductStockResource
     */
    public function update(ProductStockUpdateRequest $request, Product $product, ProductStock $stock)
    {
        $stock->update($request->validated());

        return new ProductStockResource($stock);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProductStock $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Product $product, ProductStock $stock)
    {
        $stock->delete();

        return response()->noContent();
    }
}
