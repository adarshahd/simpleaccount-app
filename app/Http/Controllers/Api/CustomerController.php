<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

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
        $customer = Customer::create($request->validated());

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
}
