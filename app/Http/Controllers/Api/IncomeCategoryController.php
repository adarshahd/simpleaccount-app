<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeCategoryRequest;
use App\Http\Resources\IncomeCategoryCollection;
use App\Http\Resources\IncomeCategoryResource;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return IncomeCategoryCollection
     */
    public function index()
    {
        $categories = IncomeCategory::query()->paginate();
        return new IncomeCategoryCollection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param IncomeCategoryRequest $request
     * @return IncomeCategoryResource
     */
    public function store(IncomeCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $category = IncomeCategory::query()->create($validatedData);

        return new IncomeCategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param IncomeCategory $category
     * @return IncomeCategoryResource
     */
    public function show(IncomeCategory $category)
    {
        return new IncomeCategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param IncomeCategoryRequest $request
     * @param IncomeCategory $category
     * @return IncomeCategoryResource
     */
    public function update(IncomeCategoryRequest $request, IncomeCategory $category)
    {
        $validatedData = $request->validated();
        $category->update($validatedData);

        return new IncomeCategoryResource($category->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param IncomeCategory $category
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(IncomeCategory $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
