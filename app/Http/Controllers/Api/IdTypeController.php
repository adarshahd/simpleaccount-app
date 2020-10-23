<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IdTypeStoreRequest;
use App\Http\Requests\IdTypeUpdateRequest;
use App\Http\Resources\IdTypeCollection;
use App\Http\Resources\IdTypeResource;
use App\Models\IdType;
use Illuminate\Http\Request;

class IdTypeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\IdTypeCollection
     */
    public function index(Request $request)
    {
        $idTypes = IdType::all();

        return new IdTypeCollection($idTypes);
    }

    /**
     * @param \App\Http\Requests\IdTypeStoreRequest $request
     * @return \App\Http\Resources\IdTypeResource
     */
    public function store(IdTypeStoreRequest $request)
    {
        $idType = IdType::create($request->validated());

        return new IdTypeResource($idType);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IdType $idType
     * @return \App\Http\Resources\IdTypeResource
     */
    public function show(Request $request, IdType $idType)
    {
        return new IdTypeResource($idType);
    }

    /**
     * @param \App\Http\Requests\IdTypeUpdateRequest $request
     * @param \App\Models\IdType $idType
     * @return \App\Http\Resources\IdTypeResource
     */
    public function update(IdTypeUpdateRequest $request, IdType $idType)
    {
        $idType->update($request->validated());

        return new IdTypeResource($idType);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IdType $idType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, IdType $idType)
    {
        $idType->delete();

        return response()->noContent();
    }
}
