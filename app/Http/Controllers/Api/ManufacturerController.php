<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManufacturerStoreRequest;
use App\Http\Requests\ManufacturerUpdateRequest;
use App\Http\Resources\ManufacturerCollection;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ManufacturerCollection
     */
    public function index(Request $request)
    {
        $manufacturers = Manufacturer::all();

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
}
