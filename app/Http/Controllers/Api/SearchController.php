<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
    public function globalSearch(Request $request) {
        if(!$request->has('query')) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $queryParam = $request->input('query');
        $searchData = collect();

        //Customers
        $customers = Customer::query()
            ->where('name', 'like', '%' . $queryParam . '%')
            ->select(['id', 'name', 'city']);
        if($customers->count() > 0) {
            $customerData = new \stdClass();
            $customerData->type = 'Customers';
            $customerData->items = $customers->get()->transform(function ($item) {
                $item->name = $item->name . ', ' . $item->city;
                $item->target = 'customer';
                return $item;
            })->toArray();

            $searchData->push($customerData);
        }

        //Vendors
        $vendors = Vendor::query()
            ->where('name', 'like', '%' . $queryParam . '%')
            ->select(['id', 'name', 'city']);
        if($vendors->count() > 0) {
            $vendorData = new \stdClass();
            $vendorData->type = 'Vendors';
            $vendorData->items = $vendors->get()->transform(function ($item) {
                $item->name = $item->name . ', ' . $item->city;
                $item->target = 'vendor';
                return $item;
            })->toArray();

            $searchData->push($vendorData);
        }

        //Sales
        $sales = Sale::query()
            ->where('bill_number', 'like', '%' . $queryParam . '%')
            ->select(['id', 'bill_number']);
        if($sales->count() > 0) {
            $saleData = new \stdClass();
            $saleData->type = 'Sales';
            $saleData->items = $sales->get()->transform(function ($item) {
                $item->target = 'sale';
                return $item;
            })->toArray();

            $searchData->push($saleData);
        }

        //purchases
        $purchases = Purchase::query()
            ->where('bill_number', 'like', '%' . $queryParam . '%')
            ->select(['id', 'bill_number']);
        if($purchases->count() > 0) {
            $purchaseData = new \stdClass();
            $purchaseData->type = 'Purchases';
            $purchaseData->items = $purchases->get()->transform(function ($item) {
                $item->target = 'purchase';
                return $item;
            })->toArray();

            $searchData->push($purchaseData);
        }

        return response()->json([
            'data' => $searchData
        ]);
    }
}
