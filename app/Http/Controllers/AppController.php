<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\AppSettingController;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index(Request $request) {
        if(AppSettingController::isApplicationInitialized()) {
            return view('app');
        }

        return redirect('/onboard');
    }
}
