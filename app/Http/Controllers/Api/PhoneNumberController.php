<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhoneNumberStoreRequest;
use App\Http\Requests\PhoneNumberUpdateRequest;
use App\Http\Resources\PhoneNumberCollection;
use App\Http\Resources\PhoneNumberResource;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\PhoneNumberCollection
     */
    public function index(Request $request)
    {
        $phoneNumbers = PhoneNumber::all();

        return new PhoneNumberCollection($phoneNumbers);
    }

    /**
     * @param \App\Http\Requests\PhoneNumberStoreRequest $request
     * @return \App\Http\Resources\PhoneNumberResource
     */
    public function store(PhoneNumberStoreRequest $request)
    {
        $phoneNumber = PhoneNumber::create($request->validated());

        return new PhoneNumberResource($phoneNumber);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PhoneNumber $phoneNumber
     * @return \App\Http\Resources\PhoneNumberResource
     */
    public function show(Request $request, PhoneNumber $phoneNumber)
    {
        return new PhoneNumberResource($phoneNumber);
    }

    /**
     * @param \App\Http\Requests\PhoneNumberUpdateRequest $request
     * @param \App\Models\PhoneNumber $phoneNumber
     * @return \App\Http\Resources\PhoneNumberResource
     */
    public function update(PhoneNumberUpdateRequest $request, PhoneNumber $phoneNumber)
    {
        $phoneNumber->update($request->validated());

        return new PhoneNumberResource($phoneNumber);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PhoneNumber $phoneNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PhoneNumber $phoneNumber)
    {
        $phoneNumber->delete();

        return response()->noContent();
    }
}
