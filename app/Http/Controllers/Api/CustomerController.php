<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Receipt;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\CustomerCollection
     */
    public function index(Request $request)
    {
        $customers = Customer::query()->latest()->paginate();

        return new CustomerCollection($customers);
    }

    /**
     * @param \App\Http\Requests\CustomerStoreRequest $request
     * @return \App\Http\Resources\CustomerResource
     */
    public function store(CustomerStoreRequest $request)
    {
        $customer = Customer::query()->create($request->validated());

        if($request->hasFile('image')) {
            $customer->addMediaFromRequest('image')->toMediaCollection('avatars');
        }

        return new CustomerResource($customer);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return \App\Http\Resources\CustomerResource
     */
    public function show(Request $request, Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * @param \App\Http\Requests\CustomerUpdateRequest $request
     * @param \App\Models\Customer $customer
     * @return \App\Http\Resources\CustomerResource
     */
    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        if($request->hasFile('image')) {
            $avatar = $customer->getFirstMedia('avatars');
            if($avatar) {
                $avatar->delete();
            }
            $customer->addMediaFromRequest('image')->toMediaCollection('avatars');
        }

        return new CustomerResource($customer);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Customer $customer)
    {
        $customer->delete();

        return response()->noContent();
    }

    public function details(Request $request, Customer $customer) {
        $recentSales = Sale::query()->where('customer_id', $customer->id)->latest()->take(4)->get();
        $recentReceipts = Receipt::query()->where('customer_id', $customer->id)->latest()->take(4)->get();
        $recentActivities = collect();
        for($i=0; $i < 4; $i++) {
            if($i < $recentSales->count()) {
                $sale = $recentSales[$i];
                $saleDetails = new \stdClass();
                $saleDetails->id = $sale->id;
                $saleDetails->bill_number = $sale->bill_number;
                $saleDetails->total = round($sale->total);
                $saleDetails->items = $sale->saleItems->count();
                $saleDetails->bill_date = $sale->bill_date;
                $saleDetails->type = 'sale';

                $recentActivities->push($saleDetails);
            }

            if($i < $recentReceipts->count()) {
                $receipt = $recentReceipts[$i];
                $receiptDetails = new \stdClass();
                $receiptDetails->id = $receipt->id;
                $receiptDetails->bill_number = $receipt->bill_number;
                $receiptDetails->total = round($receipt->total);
                $receiptDetails->items = 0;
                $receiptDetails->bill_date = $receipt->bill_date;
                $receiptDetails->type = 'receipt';

                $recentActivities->push($receiptDetails);
            }
        }

        $recentActivities = $recentActivities->sortByDesc(function ($activity, $key) {
            return Carbon::parse($activity->bill_date)->getTimestamp();
        })->values();

        $saleQuery = Sale::query()->where('customer_id', $customer->id);
        $totalSales = $saleQuery->count();
        $totalSaleAmount = round($saleQuery->sum('total'));
        $amountPaid = round($customer->receipts()->sum('total'));
        $balance = round($totalSaleAmount - $amountPaid);

        return response()->json([
            'data' => [
                'customer' => new CustomerResource($customer),
                'id_type' => $customer->idType == null ? '' : $customer->idType->name,
                'total_sales' => $totalSales,
                'recent_activities' => $recentActivities,
                'total_sale_amount' => $totalSaleAmount,
                'amount_paid' => $amountPaid,
                'balance' => $balance
            ]
        ]);
    }

    public function find(Request $request) {
        if(!$request->has('name')) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $customers = Customer::query()->where('name', 'like', $request->get('name') . "%")
            ->take(10)->get();
        return new CustomerCollection($customers);
    }
}
