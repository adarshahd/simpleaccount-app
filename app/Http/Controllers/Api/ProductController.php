<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductStockCollection;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ProductCollection
     */
    public function index(Request $request)
    {
        if($request->has('manufacturer_id')) {
            $product_ids = ProductStock::query()
                ->where('manufacturer_id', $request->input('manufacturer_id'))
                ->pluck('product_id');
            $products = Product::query()->whereIn('id', $product_ids)->latest()->paginate();
        } else {
            $products = Product::query()->latest()->paginate();
        }

        return new ProductCollection($products);
    }

    /**
     * @param \App\Http\Requests\ProductStoreRequest $request
     * @return \App\Http\Resources\ProductResource
     */
    public function store(ProductStoreRequest $request)
    {
        $product = Product::query()->create($request->validated());

        if($request->hasFile('images')) {
            $product->addMediaFromRequest('images')->toMediaCollection('products');
        }

        return new ProductResource($product);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \App\Http\Resources\ProductResource
     */
    public function show(Request $request, Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * @param \App\Http\Requests\ProductUpdateRequest $request
     * @param \App\Models\Product $product
     * @return \App\Http\Resources\ProductResource
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->update($request->validated());

        if($request->hasFile('images')) {
            $product->addMediaFromRequest('images')->toMediaCollection('products');
        }

        return new ProductResource($product);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Product $product)
    {
        foreach($product->getMedia('products') as $media) {
            $media->delete();
        }

        $product->delete();

        return response()->noContent();
    }

    public function find(Request $request) {
        if(!$request->has('name')) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $products = Product::query()->where('name', 'like', $request->input('name') . "%")->take(10)->get();
        return new ProductCollection($products);
    }

    public function stock(Request $request, Product $product) {
        $stockQuery = ProductStock::query()->where('product_id', $product->id);
        if(!$request->has('all_stock')) {
            $stockQuery = $stockQuery->where('stock', '>', 0);
        }

        if($request->has('manufacturer_id')) {
            $stockQuery = $stockQuery->where('manufacturer_id', $request->input('manufacturer_id'));
        }

        $stocks = $stockQuery->orderBy('expiry')->get();
        if($request->has('customer_id')) {
            $customer = Customer::query()->where('id', $request->input('customer_id'))->first();
            if($customer) {
                $stocks->transform(function($stock) use($customer) {
                    $sale = Sale::query()
                        ->where('customer_id', $customer->id)
                        ->whereHas('saleItems', function($query) use($stock) {
                            $query->where('product_stock_id', $stock->id);
                        })
                        ->latest()
                        ->first();

                    if($sale) {
                        $saleItem = $sale->saleItems()->where('product_stock_id', $stock->id)->first();
                        if($saleItem) {
                            $stock->price = $saleItem->price;
                        }
                    }

                    return $stock;
                });
            }
        }

        return new ProductStockCollection($stocks);
    }
}
