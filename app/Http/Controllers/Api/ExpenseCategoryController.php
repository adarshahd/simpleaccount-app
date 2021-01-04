<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseCategoryRequest;
use App\Http\Resources\ExpenseCategoryCollection;
use App\Http\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ExpenseCategoryCollection
     */
    public function index()
    {
        $categories = ExpenseCategory::query()->paginate();
        return new ExpenseCategoryCollection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ExpenseCategoryRequest $request
     * @return ExpenseCategoryResource
     */
    public function store(ExpenseCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $category = ExpenseCategory::query()->create($validatedData);

        return new ExpenseCategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param ExpenseCategory $category
     * @return ExpenseCategoryResource
     */
    public function show(ExpenseCategory $category)
    {
        return new ExpenseCategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ExpenseCategoryRequest $request
     * @param ExpenseCategory $category
     * @return ExpenseCategoryResource
     */
    public function update(ExpenseCategoryRequest $request, ExpenseCategory $category)
    {
        $validatedData = $request->validated();
        $category->update($validatedData);

        return new ExpenseCategoryResource($category->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ExpenseCategory $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseCategory $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
