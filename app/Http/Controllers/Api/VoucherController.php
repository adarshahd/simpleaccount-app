<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoucherStoreRequest;
use App\Http\Requests\VoucherUpdateRequest;
use App\Http\Resources\VoucherCollection;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\VoucherCollection
     */
    public function index(Request $request)
    {
        $vouchers = Voucher::all();

        return new VoucherCollection($vouchers);
    }

    /**
     * @param \App\Http\Requests\VoucherStoreRequest $request
     * @return \App\Http\Resources\VoucherResource
     */
    public function store(VoucherStoreRequest $request)
    {
        $voucher = Voucher::create($request->validated());

        return new VoucherResource($voucher);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Voucher $voucher
     * @return \App\Http\Resources\VoucherResource
     */
    public function show(Request $request, Voucher $voucher)
    {
        return new VoucherResource($voucher);
    }

    /**
     * @param \App\Http\Requests\VoucherUpdateRequest $request
     * @param \App\Models\Voucher $voucher
     * @return \App\Http\Resources\VoucherResource
     */
    public function update(VoucherUpdateRequest $request, Voucher $voucher)
    {
        $voucher->update($request->validated());

        return new VoucherResource($voucher);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Voucher $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Voucher $voucher)
    {
        $voucher->delete();

        return response()->noContent();
    }
}
