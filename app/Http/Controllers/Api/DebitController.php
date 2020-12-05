<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DebitException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DebitStoreRequest;
use App\Http\Requests\DebitUpdateRequest;
use App\Http\Resources\DebitCollection;
use App\Http\Resources\DebitResource;
use App\Models\Debit;
use App\Models\DebitItem;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DebitController extends Controller
{
    public static $invoiceItemsPerPage = 20;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\DebitCollection
     */
    public function index(Request $request)
    {
        $debits = Debit::all();

        return new DebitCollection($debits);
    }

    /**
     * @param \App\Http\Requests\DebitStoreRequest $request
     * @return DebitResource|\Illuminate\Http\JsonResponse
     */
    public function store(DebitStoreRequest $request)
    {
        $items = $request->input('items');

        try {
            DB::beginTransaction();

            $billPrefix = AppSettingController::getDebitBillPrefix();
            if($billPrefix == '') {
                $billPrefix = 'DB0000';
            }
            $latestDebit = Debit::query()->latest()->first();
            if($latestDebit == null) {
                $lastBillNumber = 0;
            } else {
                $lastBillNumber = $latestDebit->bill_id;
            }

            $billNumber = "$billPrefix" . ($lastBillNumber + 1);

            $debit = Debit::query()->create([
                'bill_id' => $lastBillNumber + 1,
                'bill_number' => $billNumber,
                'bill_date' => $request->input('bill_date'),
                'sub_total' => 0,
                'discount' => 0,
                'tax' => 0,
                'total' => 0,
                'vendor_id' => $request->input('vendor_id'),
            ]);

            $debitItemAddResult = $this->addDebitItems($debit, $items);

            $subTotal = $debitItemAddResult['sub_total'];
            $tax = $debitItemAddResult['tax'];
            $total = ($subTotal + $tax);

            $debit->update([
                'sub_total' => $subTotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            DB::commit();

            return new DebitResource($debit->refresh());

        } catch (\Exception $exception) {
            Log::error("Could not store debit. Error message: " . $exception->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage(),
                'description' => $exception->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Debit $debit
     * @return \App\Http\Resources\DebitResource
     */
    public function show(Request $request, Debit $debit)
    {
        return new DebitResource($debit);
    }

    /**
     * @param \App\Http\Requests\DebitUpdateRequest $request
     * @param \App\Models\Debit $debit
     * @return DebitResource|\Illuminate\Http\JsonResponse
     */
    public function update(DebitUpdateRequest $request, Debit $debit)
    {
        $items = $request->input('items');

        try {
            DB::beginTransaction();

            foreach($debit->debitItems()->get() as $debitItem) {
                $currentStock = $debitItem->productStock;
                $currentStock->update([
                    'stock' => $currentStock->stock + $debitItem->quantity,
                    'total_stock' => $currentStock->total_stock + $debitItem->quantity,
                ]);

                $debitItem->delete();
            }

            $debitItemAddResult = $this->addDebitItems($debit, $items);

            $subTotal = $debitItemAddResult['sub_total'];
            $tax = $debitItemAddResult['tax'];
            $discount = $request->input('discount');

            $total = ($subTotal + $tax) - $discount;

            $debit->update([
                'bill_date' => $request->input('bill_date'),
                'sub_total' => $subTotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'vendor_id' => $request->input('vendor_id')
            ]);

            DB::commit();

            return new DebitResource($debit->refresh());
        } catch (\Exception $exception) {
            Log::error("Could not store debit. Error message: " . $exception);
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage(),
                'description' => $exception->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Debit $debit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Debit $debit)
    {
        $debit->delete();

        return response()->noContent();
    }

    public function addDebitItems(Debit $debit, array $items) {
        $subTotal = 0;
        $tax = 0;

        foreach($items as $debitItem) {
            $productStock = ProductStock::query()->find($debitItem['product_stock_id']);
            $product = Product::query()->find($productStock->product_id);

            if($productStock == null) {
                $productName = $product->name;
                throw new DebitException("No stock found for $productName. Please check and try again!");
            }

            if($productStock->stock < $debitItem['quantity']) {
                $productName = $product->name;
                throw new DebitException("Not enough stock of $productName. Please check and try again!");
            }

            $price = $debitItem['price'];
            $taxPercent = $product->tax->tax;
            $taxExcludedPrice = ($price / (( $taxPercent / 100 ) + 1 ));
            $debitItemPrice = ($taxExcludedPrice * $debitItem['quantity']);
            $debitItemTax = $debitItemPrice * ($taxPercent / 100);
            $subTotal = $subTotal + $debitItemPrice;
            $tax = $tax + $debitItemTax;

            $productStock->update([
                'stock' => $productStock->stock - $debitItem['quantity'],
                'total_stock' => $productStock->total_stock - $debitItem['quantity']
            ]);

            DebitItem::query()->create([
                'price' => $taxExcludedPrice,
                'quantity' => $debitItem['quantity'],
                'tax_percent' => $taxPercent,
                'tax' => $debitItemTax,
                'sub_total' => $debitItemPrice,
                'total' => $debitItemPrice + $debitItemTax,
                'debit_id' => $debit->id,
                'purchase_id' => isset($debitItem['purchase_id']) ? $debitItem['purchase_id'] : null,
                'product_stock_id' => $productStock->id,
            ]);
        }

        return [
            'sub_total' => $subTotal,
            'tax' => $tax
        ];
    }

    public function invoice(Request $request, Debit $debit) {
        $productOwnerData = AppSettingController::getProductOwnerData();
        $debitItemsCount = $debit->debitItems()->count();
        $taxGroupList = $debit->debitItems()->select('debit_items.tax_percent', DB::raw('sum(debit_items.sub_total) as total'))->groupBy('tax_percent')->get();

        $pageCount = $this->getPdfPageCount($debitItemsCount);
        $pages = collect();
        if($pageCount > 1) {
            $currentPage = 1;
            $debit->debitItems()->chunk(self::$invoiceItemsPerPage, function ($items) use($debit ,$pages, $productOwnerData, $taxGroupList, $pageCount, &$currentPage) {
                $page = View::make('invoices.debits.template-1.invoice', compact('debit', 'items', 'productOwnerData', 'taxGroupList', 'pageCount', 'currentPage'));
                $pages->push($page);
                $currentPage++;
            });
        } else {
            $items = $debit->debitItems;
            $currentPage = 1;
            $page = View::make('invoices.debits.template-1.invoice', compact('debit', 'items', 'productOwnerData', 'taxGroupList', 'pageCount', 'currentPage'));
            $pages->push($page);
        }

        $invoiceFooter = View::make('invoices.debits.template-1.footer')->render();
        $pdf = \PDF::loadHtml($pages->toArray());
        return $pdf->setOption('footer-html', $invoiceFooter)->stream('Invoice-' . $debit->bill_number . '.pdf');
    }

    private function getPdfPageCount($debitItemsCount) {
        $pages = (int)($debitItemsCount / self::$invoiceItemsPerPage);
        if($debitItemsCount > self::$invoiceItemsPerPage && $debitItemsCount % self::$invoiceItemsPerPage > 0) {
            $pages++;
        }
        if($pages == 0) {
            $pages = 1;
        }

        return $pages;
    }
}
