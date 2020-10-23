<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditStoreRequest;
use App\Http\Requests\CreditUpdateRequest;
use App\Http\Resources\CreditCollection;
use App\Http\Resources\CreditResource;
use App\Models\Credit;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\CreditCollection
     */
    public function index(Request $request)
    {
        $credits = Credit::all();

        return new CreditCollection($credits);
    }

    /**
     * @param \App\Http\Requests\CreditStoreRequest $request
     * @return \App\Http\Resources\CreditResource
     */
    public function store(CreditStoreRequest $request)
    {
        $credit = Credit::create($request->validated());

        return new CreditResource($credit);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Credit $credit
     * @return \App\Http\Resources\CreditResource
     */
    public function show(Request $request, Credit $credit)
    {
        return new CreditResource($credit);
    }

    /**
     * @param \App\Http\Requests\CreditUpdateRequest $request
     * @param \App\Models\Credit $credit
     * @return \App\Http\Resources\CreditResource
     */
    public function update(CreditUpdateRequest $request, Credit $credit)
    {
        $credit->update($request->validated());

        return new CreditResource($credit);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Credit $credit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Credit $credit)
    {
        $credit->delete();

        return response()->noContent();
    }
}
