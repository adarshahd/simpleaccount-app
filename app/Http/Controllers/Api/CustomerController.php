<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Sale;
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
        $customers = Customer::all();

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
        $recentSaleDetails = collect();
        foreach ($recentSales as $sale) {
            $saleDetails = new \stdClass();
            $saleDetails->id = $sale->id;
            $saleDetails->bill_number = $sale->bill_number;
            $saleDetails->total = $sale->total;
            $saleDetails->items = $sale->saleItems->count();
            $saleDetails->bill_date = $sale->bill_date;

            $recentSaleDetails->push($saleDetails);
        }
        $saleQuery = Sale::query()->where('customer_id', $customer->id);
        $totalSales = $saleQuery->count();
        $totalSaleAmount = $saleQuery->sum('total');
        $amountPaid = $customer->receipts()->sum('total');
        $balance = $totalSaleAmount - $amountPaid;

        return response()->json([
            'data' => [
                'customer' => new CustomerResource($customer),
                'id_type' => $customer->idType->name,
                'total_sales' => $totalSales,
                'recent_sales' => $recentSaleDetails,
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
