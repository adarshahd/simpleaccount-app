<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoucherStoreRequest;
use App\Http\Requests\VoucherUpdateRequest;
use App\Http\Resources\VoucherCollection;
use App\Http\Resources\VoucherResource;
use App\Models\Purchase;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

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
        $billPrefix = AppSettingController::getVoucherBillPrefix();
        if($billPrefix == '') {
            $billPrefix = 'RE0000';
        }
        $latestVoucher = Voucher::query()->latest()->first();
        if($latestVoucher == null) {
            $lastBillNumber = 0;
        } else {
            $lastBillNumber = $latestVoucher->bill_id;
        }

        $billNumber = "$billPrefix" . ($lastBillNumber + 1);

        $voucher = Voucher::query()->create([
            'bill_id' => $lastBillNumber + 1,
            'bill_number' => $billNumber,
            'bill_date' => $request->input('bill_date'),
            'total' => $request->input('total'),
            'payment_method' => $request->input('payment_method'),
            'payment_reference' => $request->input('payment_reference'),
            'notes' => $request->input('notes'),
            'vendor_id' => $request->input('vendor_id'),
        ]);

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
        $voucher->update([
            'bill_date' => $request->input('bill_date'),
            'total' => $request->input('total'),
            'payment_method' => $request->input('payment_method'),
            'payment_reference' => $request->input('payment_reference'),
            'notes' => $request->input('notes'),
            'vendor_id' => $request->input('vendor_id'),
        ]);

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

    public function invoice(Request $request, Voucher $voucher) {
        $productOwnerData = AppSettingController::getProductOwnerData();
        $vendor = $voucher->vendor;
        $totalPurchaseAmount = Purchase::query()->where('vendor_id', $vendor->id)->sum('total');
        $totalAmountPaid = Voucher::query()->where('vendor_id', $vendor->id)->sum('total');
        $balanceAmount = $totalPurchaseAmount - $totalAmountPaid;

        $page = View::make('invoices.vouchers.template-1.voucher', compact('voucher', 'productOwnerData', 'vendor', 'balanceAmount'));

        $pdf = \PDF::loadHtml($page);
        return $pdf->stream('Voucher-' . $voucher->bill_number . '.pdf');
    }
}
