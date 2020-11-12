<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditStoreRequest;
use App\Http\Requests\CreditUpdateRequest;
use App\Http\Resources\CreditCollection;
use App\Http\Resources\CreditResource;
use App\Models\Credit;
use App\Models\CreditItem;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class CreditController extends Controller
{
    public static $invoiceItemsPerPage = 20;
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\CreditCollection
     */
    public function index(Request $request)
    {
        $credits = Credit::all();

        return new CreditCollection($credits);
    }

    /**
     * @param \App\Http\Requests\CreditStoreRequest $request
     * @return CreditResource|\Illuminate\Http\JsonResponse
     */
    public function store(CreditStoreRequest $request)
    {
        $items = $request->input('items');

        try {
            DB::beginTransaction();

            $appSettingController = new AppSettingController();
            $billPrefix = $appSettingController->getCreditBillPrefix();
            if($billPrefix == '') {
                $billPrefix = 'CR0000';
            }
            $latestCredit = Credit::query()->latest()->first();
            if($latestCredit == null) {
                $lastBillNumber = 0;
            } else {
                $lastBillNumber = $latestCredit->bill_id;
            }

            $billNumber = "$billPrefix" . ($lastBillNumber + 1);

            $credit = Credit::query()->create([
                'bill_id' => $lastBillNumber + 1,
                'bill_number' => $billNumber,
                'bill_date' => $request->input('bill_date'),
                'sub_total' => 0,
                'discount' => 0,
                'tax' => 0,
                'total' => 0,
                'customer_id' => $request->input('customer_id'),
            ]);

            $creditItemAddResult = $this->addCreditItems($credit, $items);

            $subTotal = $creditItemAddResult['sub_total'];
            $tax = $creditItemAddResult['tax'];
            $total = ($subTotal + $tax);

            $credit->update([
                'sub_total' => $subTotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            DB::commit();

            return new CreditResource($credit->refresh());
        } catch (\Exception $exception) {
            Log::error("Could not complete credit creation. Message: " . $exception->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage(),
                'description' => $exception->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Credit $credit
     * @return \App\Http\Resources\CreditResource
     */
    public function show(Request $request, Credit $credit)
    {
        return new CreditResource($credit);
    }

    /**
     * @param \App\Http\Requests\CreditUpdateRequest $request
     * @param \App\Models\Credit $credit
     * @return CreditResource|\Illuminate\Http\JsonResponse
     */
    public function update(CreditUpdateRequest $request, Credit $credit)
    {
        $items = $request->input('items');

        try {
            DB::beginTransaction();

            $creditItemCollection = collect($items);
            foreach($credit->creditItems as $creditItem) {
                $productStock = $creditItem->productStock;
                $newStockItems = $creditItemCollection->whereIn('product_stock_id', $productStock->id);
                if($newStockItems->count() < 1 && $creditItem->quantity > $productStock->stock) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'There is not enough stock of ' . $creditItem->productStock->product->name . '!',
                        'description' => 'There is not enough stock of ' . $creditItem->productStock->product->name . '!'
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                if($newStockItems->count() > 0 && ($newStockItems->sum('quantity') + $productStock->stock - $creditItem->quantity) < 0) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'There is not enough stock of ' . $creditItem->productStock->product->name . '!',
                        'description' => 'There is not enough stock of ' . $creditItem->productStock->product->name . '!'
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $productStock->update([
                    'stock' => $productStock->stock - $creditItem->quantity
                ]);

                $creditItem->delete();
            }

            $creditItemAddResult = $this->addCreditItems($credit, $items);

            $subTotal = $creditItemAddResult['sub_total'];
            $tax = $creditItemAddResult['tax'];
            $total = ($subTotal + $tax);

            $credit->update([
                'sub_total' => $subTotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            DB::commit();

            return new CreditResource($credit->refresh());
        } catch (\Exception $exception) {
            Log::error("Could not complete credit creation. Message: " . $exception->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage(),
                'description' => $exception->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Credit $credit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Credit $credit)
    {
        $credit->delete();

        return response()->noContent();
    }

    private function addCreditItems($credit, $creditItems) {
        $subTotal = 0;
        $tax = 0;

        foreach($creditItems as $creditItem) {
            $productStock = ProductStock::query()->find($creditItem['product_stock_id']);
            $product = Product::query()->find($productStock->product_id);
            $price = $creditItem['price'];
            $taxPercent = $product->tax->tax;
            $taxExcludedPrice = ($price / (( $taxPercent / 100 ) + 1 ));
            $creditItemPrice = ($taxExcludedPrice * $creditItem['quantity']);
            $creditItemTax = $creditItemPrice * ($taxPercent / 100);
            $subTotal = $subTotal + $creditItemPrice;
            $tax = $tax + $creditItemTax;

            $productStock->update([
                'stock' => $productStock->stock + $creditItem['quantity'],
            ]);

            CreditItem::query()->create([
                'price' => $taxExcludedPrice,
                'quantity' => $creditItem['quantity'],
                'tax_percent' => $taxPercent,
                'tax' => $creditItemTax,
                'sub_total' => $creditItemPrice,
                'total' => $creditItemPrice + $creditItemTax,
                'credit_id' => $credit->id,
                'product_stock_id' => $productStock->id,
            ]);
        }

        return [
            'sub_total' => $subTotal,
            'tax' => $tax
        ];
    }

    public function invoice(Request $request, Credit $credit) {
        $productOwnerData = AppSettingController::getProductOwnerData();
        $creditItemsCount = $credit->creditItems()->count();
        $taxGroupList = $credit->creditItems()->select('sale_items.tax_percent', DB::raw('sum(sale_items.sub_total) as total'))->groupBy('tax_percent')->get();

        $pageCount = $this->getPdfPageCount($creditItemsCount);
        $pages = collect();
        if($pageCount > 1) {
            $currentPage = 1;
            $credit->creditItems()->chunk(self::$invoiceItemsPerPage, function ($items) use($credit ,$pages, $productOwnerData, $taxGroupList, $pageCount, &$currentPage) {
                $page = View::make('invoices.credits.template-1.invoice', compact('sale', 'items', 'productOwnerData', 'taxGroupList', 'pageCount', 'currentPage'));
                $pages->push($page);
                $currentPage++;
            });
        } else {
            $items = $credit->creditItems;
            $currentPage = 1;
            $page = View::make('invoices.credits.template-1.invoice', compact('credit', 'items', 'productOwnerData', 'taxGroupList', 'pageCount', 'currentPage'));
            $pages->push($page);
        }

        $invoiceFooter = View::make('invoices.credits.template-1.footer')->render();
        $pdf = \PDF::loadHtml($pages->toArray());
        return $pdf->setOption('footer-html', $invoiceFooter)->stream('Invoice-' . $credit->bill_number . '.pdf');
    }

    private function getPdfPageCount($creditItemsCount) {
        $pages = (int)($creditItemsCount / self::$invoiceItemsPerPage);
        if($creditItemsCount > self::$invoiceItemsPerPage && $creditItemsCount % self::$invoiceItemsPerPage > 0) {
            $pages++;
        }
        if($pages == 0) {
            $pages = 1;
        }

        return $pages;
    }
}
