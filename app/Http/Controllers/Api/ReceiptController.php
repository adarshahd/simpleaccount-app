<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReceiptStoreRequest;
use App\Http\Requests\ReceiptUpdateRequest;
use App\Http\Resources\ReceiptCollection;
use App\Http\Resources\ReceiptResource;
use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ReceiptCollection
     */
    public function index(Request $request)
    {
        $receipts = Receipt::all();

        return new ReceiptCollection($receipts);
    }

    /**
     * @param \App\Http\Requests\ReceiptStoreRequest $request
     * @return \App\Http\Resources\ReceiptResource
     */
    public function store(ReceiptStoreRequest $request)
    {
        $receipt = Receipt::create($request->validated());

        return new ReceiptResource($receipt);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \App\Http\Resources\ReceiptResource
     */
    public function show(Request $request, Receipt $receipt)
    {
        return new ReceiptResource($receipt);
    }

    /**
     * @param \App\Http\Requests\ReceiptUpdateRequest $request
     * @param \App\Models\Receipt $receipt
     * @return \App\Http\Resources\ReceiptResource
     */
    public function update(ReceiptUpdateRequest $request, Receipt $receipt)
    {
        $receipt->update($request->validated());

        return new ReceiptResource($receipt);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Receipt $receipt)
    {
        $receipt->delete();

        return response()->noContent();
    }
}
