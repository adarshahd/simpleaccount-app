<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeStoreRequest;
use App\Http\Requests\IncomeUpdateRequest;
use App\Http\Resources\IncomeCollection;
use App\Http\Resources\IncomeResource;
use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\IncomeCollection
     */
    public function index(Request $request)
    {
        $incomes = Income::all();

        return new IncomeCollection($incomes);
    }

    /**
     * @param \App\Http\Requests\IncomeStoreRequest $request
     * @return \App\Http\Resources\IncomeResource
     */
    public function store(IncomeStoreRequest $request)
    {
        $income = Income::create($request->validated());

        return new IncomeResource($income);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Income $income
     * @return \App\Http\Resources\IncomeResource
     */
    public function show(Request $request, Income $income)
    {
        return new IncomeResource($income);
    }

    /**
     * @param \App\Http\Requests\IncomeUpdateRequest $request
     * @param \App\Models\Income $income
     * @return \App\Http\Resources\IncomeResource
     */
    public function update(IncomeUpdateRequest $request, Income $income)
    {
        $income->update($request->validated());

        return new IncomeResource($income);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Income $income
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Income $income)
    {
        $income->delete();

        return response()->noContent();
    }
}
