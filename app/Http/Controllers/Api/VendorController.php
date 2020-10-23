<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorStoreRequest;
use App\Http\Requests\VendorUpdateRequest;
use App\Http\Resources\VendorCollection;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\VendorCollection
     */
    public function index(Request $request)
    {
        $vendors = Vendor::all();

        return new VendorCollection($vendors);
    }

    /**
     * @param \App\Http\Requests\VendorStoreRequest $request
     * @return \App\Http\Resources\VendorResource
     */
    public function store(VendorStoreRequest $request)
    {
        $vendor = Vendor::create($request->validated());

        return new VendorResource($vendor);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vendor $vendor
     * @return \App\Http\Resources\VendorResource
     */
    public function show(Request $request, Vendor $vendor)
    {
        return new VendorResource($vendor);
    }

    /**
     * @param \App\Http\Requests\VendorUpdateRequest $request
     * @param \App\Models\Vendor $vendor
     * @return \App\Http\Resources\VendorResource
     */
    public function update(VendorUpdateRequest $request, Vendor $vendor)
    {
        $vendor->update($request->validated());

        return new VendorResource($vendor);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vendor $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Vendor $vendor)
    {
        $vendor->delete();

        return response()->noContent();
    }
}
