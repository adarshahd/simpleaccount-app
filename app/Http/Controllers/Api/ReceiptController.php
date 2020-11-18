<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReceiptStoreRequest;
use App\Http\Requests\ReceiptUpdateRequest;
use App\Http\Resources\ReceiptCollection;
use App\Http\Resources\ReceiptResource;
use App\Models\Receipt;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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
        $billPrefix = AppSettingController::getReceiptBillPrefix();
        if($billPrefix == '') {
            $billPrefix = 'RE0000';
        }
        $latestReceipt = Receipt::query()->latest()->first();
        if($latestReceipt == null) {
            $lastBillNumber = 0;
        } else {
            $lastBillNumber = $latestReceipt->bill_id;
        }

        $billNumber = "$billPrefix" . ($lastBillNumber + 1);

        $receipt = Receipt::query()->create([
            'bill_id' => $lastBillNumber + 1,
            'bill_number' => $billNumber,
            'bill_date' => $request->input('bill_date'),
            'total' => $request->input('total'),
            'payment_method' => $request->input('payment_method'),
            'payment_reference' => $request->input('payment_reference'),
            'notes' => $request->input('notes'),
            'customer_id' => $request->input('customer_id'),
        ]);

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

    public function invoice(Request $request, Receipt $receipt) {
        $productOwnerData = AppSettingController::getProductOwnerData();
        $customer = $receipt->customer;
        $totalSaleAmount = Sale::query()->where('customer_id', $customer->id)->sum('total');
        $totalAmountPaid = Receipt::query()->where('customer_id', $customer->id)->sum('total');
        $balanceAmount = $totalSaleAmount - $totalAmountPaid;

        $page = View::make('invoices.receipts.template-1.receipt', compact('receipt', 'productOwnerData', 'customer', 'balanceAmount'));

        $pdf = \PDF::loadHtml($page);
        return $pdf->stream('Receipt-' . $receipt->bill_number . '.pdf');
    }
}
