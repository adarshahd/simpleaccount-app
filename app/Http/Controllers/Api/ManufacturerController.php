<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManufacturerStoreRequest;
use App\Http\Requests\ManufacturerUpdateRequest;
use App\Http\Resources\ManufacturerCollection;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturer;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManufacturerController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ManufacturerCollection
     */
    public function index(Request $request)
    {
        $manufacturers = Manufacturer::query()->latest()->paginate();

        return new ManufacturerCollection($manufacturers);
    }

    /**
     * @param \App\Http\Requests\ManufacturerStoreRequest $request
     * @return \App\Http\Resources\ManufacturerResource
     */
    public function store(ManufacturerStoreRequest $request)
    {
        $manufacturer = Manufacturer::create($request->validated());

        return new ManufacturerResource($manufacturer);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Manufacturer $manufacturer
     * @return \App\Http\Resources\ManufacturerResource
     */
    public function show(Request $request, Manufacturer $manufacturer)
    {
        return new ManufacturerResource($manufacturer);
    }

    /**
     * @param \App\Http\Requests\ManufacturerUpdateRequest $request
     * @param \App\Models\Manufacturer $manufacturer
     * @return \App\Http\Resources\ManufacturerResource
     */
    public function update(ManufacturerUpdateRequest $request, Manufacturer $manufacturer)
    {
        $manufacturer->update($request->validated());

        return new ManufacturerResource($manufacturer);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Manufacturer $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Manufacturer $manufacturer)
    {
        $manufacturer->delete();

        return response()->noContent();
    }

    public function find(Request $request) {
        if($request->has('product_id')) {
            $ids = ProductStock::query()->where('product_id', $request->get('product_id'))->pluck('manufacturer_id')->take(10)->toArray();
            $manufacturers = Manufacturer::query()->whereIn('id', $ids)->get();

            return response()->json([
                'data' => $manufacturers
            ]);
        }

        if($request->has('name')) {
            $searchName = $request->input('name');
            $manufacturers = Manufacturer::query()->where('name', 'like', $searchName . "%")->get();
            return response()->json([
                'data' => $manufacturers
            ]);
        }

        return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
