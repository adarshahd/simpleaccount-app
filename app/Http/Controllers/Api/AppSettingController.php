<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppSettingStoreRequest;
use App\Http\Requests\AppSettingUpdateRequest;
use App\Http\Resources\AppSettingCollection;
use App\Http\Resources\AppSettingResource;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\AppSettingCollection
     */
    public function index(Request $request)
    {
        $appSettings = AppSetting::all();

        return new AppSettingCollection($appSettings);
    }

    /**
     * @param \App\Http\Requests\AppSettingStoreRequest $request
     * @return \App\Http\Resources\AppSettingResource
     */
    public function store(AppSettingStoreRequest $request)
    {
        $appSetting = AppSetting::create($request->validated());

        return new AppSettingResource($appSetting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AppSetting $appSetting
     * @return \App\Http\Resources\AppSettingResource
     */
    public function show(Request $request, AppSetting $appSetting)
    {
        return new AppSettingResource($appSetting);
    }

    /**
     * @param \App\Http\Requests\AppSettingUpdateRequest $request
     * @param \App\Models\AppSetting $appSetting
     * @return \App\Http\Resources\AppSettingResource
     */
    public function update(AppSettingUpdateRequest $request, AppSetting $appSetting)
    {
        $appSetting->update($request->validated());

        return new AppSettingResource($appSetting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AppSetting $appSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, AppSetting $appSetting)
    {
        $appSetting->delete();

        return response()->noContent();
    }
}
