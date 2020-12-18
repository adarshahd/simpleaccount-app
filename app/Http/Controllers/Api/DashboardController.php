<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Return dashboard data, which will include
     * 1. total revenue
     * 2. total sales
     * 3. total purchase
     * 4. total stock with price
     * 5. sales chart data (date, sale total)
     * @param Request $request
     */
    public function index(Request $request) {
        if($request->has('filter')) {
            $filter = $request->input('filter');
        } else {
            $filter = '0';
        }

        $labels = collect();
        $salesData = collect();
        switch ($filter) {
            case '1':
                $range = [Carbon::now()->subDays(30)->startOfDay(), Carbon::now()->endOfDay()];
                for($i = 30; $i > 0; --$i) {
                    $date = Carbon::now()->subDays($i);
                    $saleQuery = Sale::query()->whereDate('bill_date', $date);
                    $saleData = new \stdClass();

                    $saleData->meta = 'Sale Total ₹' . number_format($saleQuery->sum('total'), 2);
                    $saleData->value = $saleQuery->count();
                    $salesData->push($saleData);
                    $labels->push($date->format('d/m'));
                }
                break;
            case '2':
                $range = [Carbon::now()->startOfYear()->startOfDay(), Carbon::now()->endOfDay()];
                for($i = 0; $i < Carbon::now()->month; ++$i) {
                    $date = Carbon::now()->startOfYear()->addMonths($i);
                    $saleQuery = Sale::query()->whereBetween('bill_date', [$date->copy()->startOfMonth()->startOfDay(), $date->copy()->endOfMonth()->endOfDay()]);
                    $saleData = new \stdClass();

                    $saleData->meta = 'Sale Total ₹' . number_format($saleQuery->sum('total'), 2);
                    $saleData->value = $saleQuery->count();
                    $salesData->push($saleData);
                    $labels->push($date->format('M'));
                }
                break;
            case '3':
                $range = [Carbon::now()->subYear()->startOfYear()->startOfDay(), Carbon::now()->subYear()->endOfYear()->endOfDay()];
                for($i = 0; $i < 12; ++$i) {
                    $date = Carbon::now()->subYear()->startOfYear()->addMonths($i);
                    $saleQuery = Sale::query()->whereBetween('bill_date', [$date->copy()->startOfMonth()->startOfDay(), $date->copy()->endOfMonth()->endOfDay()]);
                    $saleData = new \stdClass();

                    $saleData->meta = 'Sale Total ₹' . number_format($saleQuery->sum('total'), 2);
                    $saleData->value = $saleQuery->count();
                    $salesData->push($saleData);
                    $labels->push($date->format('M'));
                }
                break;
            default:
                $range = [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()];
                for($i = 7; $i > 0; --$i) {
                    $date = Carbon::now()->subDays($i);
                    $saleQuery = Sale::query()->whereDate('bill_date', $date);
                    $saleData = new \stdClass();

                    $saleData->meta = 'Sale Total ₹' . number_format($saleQuery->sum('total'), 2);
                    $saleData->value = $saleQuery->count();
                    $salesData->push($saleData);
                    $labels->push($date->englishDayOfWeek);
                }
                break;
        }

        $saleQuery = Sale::query()->whereBetween('bill_date', $range);
        $totalSaleRevenue = $saleQuery->sum('total');
        $totalSaleCount = $saleQuery->count();
        $totalIncome = Income::query()->whereBetween('date', $range)->sum('total');
        $totalRevenue = $totalSaleRevenue + $totalIncome;

        $purchaseQuery = Purchase::query()->whereBetween('bill_date', $range);
        $totalPurchase = $purchaseQuery->sum('total');
        $totalPurchaseCount = $purchaseQuery->count();

        $stockQuery = ProductStock::query()->where('stock', '>', 0);
        $stockCount = $stockQuery->sum('stock');
        $productCount = $stockQuery->newQuery()->groupBy('product_id')->count();

        $dashboardData = new \stdClass();
        $dashboardData->sales = $totalSaleCount;
        $dashboardData->salesTotal = $totalSaleRevenue;
        $dashboardData->revenue = $totalRevenue;
        $dashboardData->purchases = $totalPurchaseCount;
        $dashboardData->purchasesTotal = $totalPurchase;
        $dashboardData->products = $productCount;
        $dashboardData->stock = $stockCount;

        //$salesDataCollection = collect($salesData);

        $dashboardData->salesChartData = [
            'labels' => $labels,
            'series' => [
                $salesData
            ]
        ];

        return response()->json([
            'data' => $dashboardData
        ]);
    }
}
