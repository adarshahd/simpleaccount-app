<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\AppSettingController;
use App\Http\Requests\ProductOwnerRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class OnboardController extends Controller
{
    /*private $isApplicationInitialized;

    public function __construct()
    {
        $this->isApplicationInitialized = AppSettingController::isApplicationInitialized();
    }*/

    public function index(Request $request) {
        // If the application is already initialized redirect to admin route
        if(AppSettingController::isApplicationInitialized()) {
            return redirect('/admin');
        }

        // If Roles and Permissions are not seeded, seed the same into database
        if(!AppSettingController::isRolesAndPermissionsSeeded()) {
            Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder', '--force' => true]);
            AppSettingController::setRolesAndPermissionsSeeded();
        }

        return view('onboard');
    }

    public function getProductStatus(Request $request) {
        $adminCreated = User::role('admin')->count() > 0;
        $poUpdated = AppSettingController::isProductOwnerUpdated();

        return response()->json([
            'data' => [
                'admin_created' => $adminCreated,
                'product_owner_updated' => $poUpdated
            ]
        ]);
    }

    public function createAdminUser(UserStoreRequest $request) {
        if(User::role('admin')->count() > 0) {
            return response()->json([
                'message' => 'Admin user already created. Please use the same for login!'
            ], Response::HTTP_BAD_REQUEST);
        }

        $adminUser = User::query()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'email_verified' => true
        ]);

        $adminUser->assignRole('admin');

        Auth::login($adminUser);

        return response()->json([
            'data' => [
                'status' => true,
            ]
        ]);
    }

    public function updateProductOwnerData(ProductOwnerRequest $request) {
        if(AppSettingController::isApplicationInitialized() && !$request->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'Product Already initialized. Login to modify data!'
            ], Response::HTTP_BAD_REQUEST);
        }

        $productOwnerData = $request->validated();
        if($request->hasFile('logo')) {
            $request->file('logo')->storeAs('public/logos', 'logo.png');
        }
        $productOwnerData['logo'] = asset('storage/logos/logo.png');

        $result = AppSettingController::updateProductOwnerData(json_encode($productOwnerData));

        if($result) {
            AppSettingController::setApplicationInitialized();
            return response()->json([]);
        } else {
            return response()->json([], Response::HTTP_CONFLICT);
        }
    }
}
