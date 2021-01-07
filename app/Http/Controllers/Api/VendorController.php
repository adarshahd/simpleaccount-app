<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorStoreRequest;
use App\Http\Requests\VendorUpdateRequest;
use App\Http\Resources\VendorCollection;
use App\Http\Resources\VendorResource;
use App\Models\Purchase;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\VendorCollection
     */
    public function index(Request $request)
    {
        $vendors = Vendor::query()->latest()->paginate();

        return new VendorCollection($vendors);
    }

    /**
     * @param \App\Http\Requests\VendorStoreRequest $request
     * @return \App\Http\Resources\VendorResource
     */
    public function store(VendorStoreRequest $request)
    {
        $vendor = Vendor::query()->create($request->validated());

        if($request->hasFile('image')) {
            $vendor->addMediaFromRequest('image')->toMediaCollection('avatars');
        }

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

        if($request->hasFile('image')) {
            $avatar = $vendor->getFirstMedia('avatars');
            if($avatar) {
                $avatar->delete();
            }
            $vendor->addMediaFromRequest('image')->toMediaCollection('avatars');
        }

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

    public function details(Request $request, Vendor $vendor) {
        $recentPurchases = Purchase::query()->where('vendor_id', $vendor->id)->latest()->take(4)->get();
        $recentPurchaseDetails = collect();
        foreach ($recentPurchases as $purchase) {
            $purchaseDetails = new \stdClass();
            $purchaseDetails->id = $purchase->id;
            $purchaseDetails->bill_number = $purchase->bill_number;
            $purchaseDetails->total = $purchase->total;
            $purchaseDetails->items = $purchase->purchaseItems()->count();
            $purchaseDetails->bill_date = $purchase->bill_date;

            $recentPurchaseDetails->push($purchaseDetails);
        }
        $purchaseQuery = Purchase::query()->where('vendor_id', $vendor->id);
        $totalPurchases = $purchaseQuery->count();
        $totalPurchaseAmount = $purchaseQuery->sum('total');
        $amountPaid = $vendor->vouchers()->sum('total');
        $balance = $totalPurchaseAmount - $amountPaid;

        return response()->json([
            'data' => [
                'id_type' => $vendor->idType == null ? '' : $vendor->idType->name,
                'vendor' => new VendorResource($vendor),
                'total_purchases' => $totalPurchases,
                'recent_purchases' => $recentPurchaseDetails,
                'total_purchase_amount' => $totalPurchaseAmount,
                'amount_paid' => $amountPaid,
                'balance' => $balance
            ]
        ]);
    }

    public function find(Request $request) {
        if(!$request->has('name')) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $vendors = Vendor::query()->where('name', 'like', $request->input('name') . "%")
            ->take(10)->get();
        return new VendorCollection($vendors);
    }
}
