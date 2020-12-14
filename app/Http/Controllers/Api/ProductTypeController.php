<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTypeStoreRequest;
use App\Http\Requests\ProductTypeUpdateRequest;
use App\Http\Resources\ProductTypeCollection;
use App\Http\Resources\ProductTypeResource;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ProductTypeCollection
     */
    public function index(Request $request)
    {
        $productTypes = ProductType::query()->latest()->paginate();

        return new ProductTypeCollection($productTypes);
    }

    /**
     * @param \App\Http\Requests\ProductTypeStoreRequest $request
     * @return \App\Http\Resources\ProductTypeResource
     */
    public function store(ProductTypeStoreRequest $request)
    {
        $productType = ProductType::create($request->validated());

        return new ProductTypeResource($productType);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProductType $productType
     * @return \App\Http\Resources\ProductTypeResource
     */
    public function show(Request $request, ProductType $productType)
    {
        return new ProductTypeResource($productType);
    }

    /**
     * @param \App\Http\Requests\ProductTypeUpdateRequest $request
     * @param \App\Models\ProductType $productType
     * @return \App\Http\Resources\ProductTypeResource
     */
    public function update(ProductTypeUpdateRequest $request, ProductType $productType)
    {
        $productType->update($request->validated());

        return new ProductTypeResource($productType);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProductType $productType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ProductType $productType)
    {
        $productType->delete();

        return response()->noContent();
    }
}
