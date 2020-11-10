<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SaleException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaleStoreRequest;
use App\Http\Requests\SaleUpdateRequest;
use App\Http\Resources\SaleCollection;
use App\Http\Resources\SaleResource;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    public static $invoiceItemsPerPage = 20;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\SaleCollection
     */
    public function index(Request $request)
    {
        $sales = Sale::all();

        return new SaleCollection($sales);
    }

    /**
     * @param \App\Http\Requests\SaleStoreRequest $request
     * @return SaleResource|\Illuminate\Http\JsonResponse
     */
    public function store(SaleStoreRequest $request)
    {
        $saleItems = $request->input('sale_items');

        try {
            //Create sale and sale item entries inside a transaction
            DB::beginTransaction();

            $appSettingController = new AppSettingController();
            $billPrefix = $appSettingController->getBillPrefix();
            if($billPrefix == '') {
                $billPrefix = 'SA0000';
            }
            $latestSale = Sale::query()->latest()->first();
            if($latestSale == null) {
                $lastBillNumber = 0;
            } else {
                $lastBillNumber = $latestSale->bill_id;
            }

            $billNumber = "$billPrefix" . ($lastBillNumber + 1);

            $sale = Sale::query()->create([
                'bill_id' => $lastBillNumber + 1,
                'bill_number' => $billNumber,
                'bill_date' => $request->input('bill_date'),
                'discount' => $request->input('discount'),
                'customer_id' => $request->input('customer_id'),
                'sub_total' => 0,
                'tax' => 0,
                'total' => 0,
            ]);

            $saleItemAddResult = $this->addSaleItems($sale, $saleItems);

            $discount = $request->input('discount');
            $subTotal = $saleItemAddResult['sub_total'];
            $tax = $saleItemAddResult['tax'];
            $total = ($subTotal + $tax) - $discount;

            $sale->update([
                'sub_total' => $subTotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
            ]);

            DB::commit();

            return new SaleResource($sale->refresh());
        } catch (SaleException $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'description' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\PDOException $e) {
            DB::rollBack();

            return response()->json([
                'message' => "There were some error processing your request. Please check and try again.",
                'description' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sale $sale
     * @return \App\Http\Resources\SaleResource
     */
    public function show(Request $request, Sale $sale)
    {
        return new SaleResource($sale);
    }

    /**
     * @param \App\Http\Requests\SaleUpdateRequest $request
     * @param \App\Models\Sale $sale
     * @return SaleResource|\Illuminate\Http\JsonResponse
     */
    public function update(SaleUpdateRequest $request, Sale $sale)
    {
        $saleItems = $request->input('sale_items');

        try {
            DB::beginTransaction();

            foreach($sale->saleItems()->get() as $saleItem) {
                $currentStock = $saleItem->productStock;
                $currentStock->update([
                    'stock' => $currentStock->stock + $saleItem->quantity
                ]);

                $saleItem->delete();
            }

            $saleItemAddResult = $this->addSaleItems($sale, $saleItems);

            $subTotal = $saleItemAddResult['sub_total'];
            $tax = $saleItemAddResult['tax'];
            $discount = $request->input('discount');

            $total = ($subTotal + $tax) - $discount;

            $sale->update([
                'bill_date' => $request->input('bill_date'),
                'sub_total' => $subTotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'customer_id' => $request->input('customer_id')
            ]);

            DB::commit();

            return new SaleResource($sale->refresh());
        } catch (SaleException $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'description' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\PDOException $e) {
            DB::rollBack();

            return response()->json([
                'message' => "There were some error processing your request. Please check and try again.",
                'description' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sale $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Sale $sale)
    {
        $sale->delete();

        return response()->noContent();
    }

    private function addSaleItems($sale, $saleItems) {
        $subTotal = 0;
        $tax = 0;

        foreach($saleItems as $saleItem) {
            $productStock = ProductStock::query()->find($saleItem['product_stock_id']);
            $product = Product::query()->find($productStock->product_id);
            $discount = $saleItem['discount'];
            $price = $saleItem['price'] - $discount;
            $taxPercent = $product->tax->tax;
            $tax_excluded_price = ($price / (( $taxPercent / 100 ) + 1 ));
            $saleItemPrice = ($tax_excluded_price * $saleItem['quantity']);
            $saleItemTax = $saleItemPrice * ($taxPercent / 100);
            $subTotal = $subTotal + $saleItemPrice;
            $tax = $tax + $saleItemTax;

            if($productStock == null) {
                $productName = $product->name;
                throw new SaleException("No stock found for $productName. Please check and try again!");
            }

            if($productStock->stock < $saleItem['quantity']) {
                $productName = $product->name;
                throw new SaleException("Not enough stock of $productName. Please check and try again!");
            }

            $productStock->update([
                'stock' => $productStock->stock - $saleItem['quantity'],
            ]);

            SaleItem::query()->create([
                'price' => $tax_excluded_price,
                'discount' => $saleItem['discount'],
                'quantity' => $saleItem['quantity'],
                'tax_percent' => $taxPercent,
                'tax' => $saleItemTax,
                'sub_total' => $saleItemPrice,
                'total' => $saleItemPrice + $saleItemTax,
                'sale_id' => $sale->id,
                'product_stock_id' => $productStock->id,
            ]);
        }

        return [
            'sub_total' => $subTotal,
            'tax' => $tax
        ];
    }

    public function invoice(Request $request, Sale $sale) {
        $productOwnerData = AppSettingController::getProductOwnerData();
        $saleItemsCount = $sale->saleItems()->count();
        $taxGroupList = $sale->saleItems()->select('sale_items.tax_percent', DB::raw('sum(sale_items.sub_total) as total'))->groupBy('tax_percent')->get();

        $pageCount = $this->getPdfPageCount($saleItemsCount);
        $pages = collect();
        if($pageCount > 1) {
            $currentPage = 1;
            $sale->saleItems()->chunk(self::$invoiceItemsPerPage, function ($items) use($sale ,$pages, $productOwnerData, $taxGroupList, $pageCount, &$currentPage) {
                $page = View::make('invoices.sales.template-1.invoice', compact('sale', 'items', 'productOwnerData', 'taxGroupList', 'pageCount', 'currentPage'));
                $pages->push($page);
                $currentPage++;
            });
        } else {
            $items = $sale->saleItems;
            $currentPage = 1;
            $page = View::make('invoices.sales.template-1.invoice', compact('sale', 'items', 'productOwnerData', 'taxGroupList', 'pageCount', 'currentPage'));
            $pages->push($page);
        }

        $invoiceFooter = View::make('invoices.sales.template-1.footer')->render();
        $pdf = \PDF::loadHtml($pages->toArray());
        return $pdf->setOption('footer-html', $invoiceFooter)->stream('Invoice-' . $sale->bill_number . '.pdf');
    }

    private function getPdfPageCount($saleItemsCount) {
        $pages = (int)($saleItemsCount / self::$invoiceItemsPerPage);
        if($saleItemsCount > self::$invoiceItemsPerPage && $saleItemsCount % self::$invoiceItemsPerPage > 0) {
            $pages++;
        }
        if($pages == 0) {
            $pages = 1;
        }

        return $pages;
    }
}
