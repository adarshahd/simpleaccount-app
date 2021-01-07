<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Countries\Package\Countries;

class CountryController extends Controller
{
    public function getCountries()
    {
        $countries = new Countries();
        return $countries->all()->pluck('name.common')->toJson();
    }

    public function getStates(Request $request)
    {
        $request->validate([
            'country' => ['required', 'string']
        ]);
        $countries = new Countries();
        return $countries->where('name.common', $request->input('country'))->first()->hydrate('states')->states->pluck('name')->toJson();
    }

    public function getCities(Request $request)
    {
        $request->validate([
            'country' => ['required', 'string'],
            'state' => ['required', 'string']
        ]);
        $countries = new Countries();
        return $countries->where('name.common', $request->input('country'))->first()->hydrate('cities')->cities->where('adm1name', $request->input('state'))->pluck('name')->toJson();
    }
}
