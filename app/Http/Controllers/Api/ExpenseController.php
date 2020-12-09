<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Http\Resources\ExpenseCollection;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ExpenseCollection
     */
    public function index(Request $request)
    {
        $expenses = Expense::query()->latest('date')->paginate();

        return new ExpenseCollection($expenses);
    }

    /**
     * @param \App\Http\Requests\ExpenseStoreRequest $request
     * @return \App\Http\Resources\ExpenseResource
     */
    public function store(ExpenseStoreRequest $request)
    {
        $expense = Expense::query()->create($request->validated());

        return new ExpenseResource($expense);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Expense $expense
     * @return \App\Http\Resources\ExpenseResource
     */
    public function show(Request $request, Expense $expense)
    {
        return new ExpenseResource($expense);
    }

    /**
     * @param \App\Http\Requests\ExpenseUpdateRequest $request
     * @param \App\Models\Expense $expense
     * @return \App\Http\Resources\ExpenseResource
     */
    public function update(ExpenseUpdateRequest $request, Expense $expense)
    {
        $expense->update($request->validated());

        return new ExpenseResource($expense);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Expense $expense)
    {
        $expense->delete();

        return response()->noContent();
    }
}
