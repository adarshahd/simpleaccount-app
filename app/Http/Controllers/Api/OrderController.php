<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Rules\OrderItemRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public static $orderTypePurchase = 'purchase_order';

    public static $orderTypeSale = 'sale_order';

    public static $orderStatusCreated = 'create';

    public static $orderStatusProcessing = 'processing';

    public static $orderStatusCompleted = 'completed';

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return OrderCollection|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if($request->has('type')) {
            if($request->input('type') == self::$orderTypeSale) {
                return $this->getSaleOrders();
            }

            if($request->input('type') == self::$orderTypePurchase) {
                return $this->getPurchaseOrders();
            }

            return response()->json([
                'data' => collect()
            ]);
        }

        return new OrderCollection(Order::query()->paginate());
    }

    /**
     * Return list of sale orders
     *
     * @return OrderCollection
     */
    public function getSaleOrders() {
        return new OrderCollection(Order::query()->where('order_type', self::$orderTypeSale)->paginate());
    }

    /**
     * Return list of purchase orders
     *
     * @return OrderCollection
     */
    public function getPurchaseOrders() {
        return new OrderCollection(Order::query()->where('order_type', self::$orderTypePurchase)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return OrderResource|\Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request)
    {
        $validatedData = $request->validated();
        if($validatedData['order_type'] == self::$orderTypeSale) {
            $prefix = "SO-";
        } else {
            $prefix = "PO-";
        }

        try {

            $order = Order::query()->create([
                'order_id' => uniqid($prefix, true),
                'order_type' => $validatedData['order_type'],
                'order_status' => 'created',
                'customer_id' => isset($validatedData['customer_id']) ? $validatedData['customer_id'] : null,
                'vendor_id' => isset($validatedData['vendor_id']) ? $validatedData['vendor_id'] : null
            ]);

            $this->addOrderItems($order, $validatedData);

            return new OrderResource($order->refresh());
        } catch (\Exception $exception) {
            Log::error('Error creating order: ' . $exception->getMessage());
            return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return OrderResource
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return OrderResource|\Illuminate\Http\JsonResponse
     */
    public function update(OrderRequest $request, Order $order)
    {
        if($order->sale != null || $order->purchase != null) {
            return response()->json([
                'error' => 'Order update not possible!',
                'message' => 'This order can not be updated since there is already a purchase/sale associated with the order'
            ], Response::HTTP_BAD_REQUEST);
        }

        $validatedData = $request->validated();

        try {
            foreach ($order->orderItems as $item) {
                $item->delete();
            }

            $order->update([
                'order_type' => $validatedData['order_type'],
                'order_status' => isset($validatedData['order_status']) ? $validatedData['order_status'] : $order->order_status,
                'customer_id' => isset($validatedData['customer_id']) ? $validatedData['customer_id'] : null,
                'vendor_id' => isset($validatedData['vendor_id']) ? $validatedData['vendor_id'] : null
            ]);

            $this->addOrderItems($order, $validatedData);
            return new OrderResource($order->refresh());
        } catch (\Exception $exception) {
            Log::error('Error updating Order: ' . $exception->getMessage());
            return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if($order->sale != null || $order->purchase != null) {
            return response()->json([
                'error' => 'Order delete not possible!',
                'message' => 'This order can not be deleted since there is already a purchase/sale associated with the order'
            ], Response::HTTP_BAD_REQUEST);
        }

        foreach($order->orderItems as $item) {
            $item->delete();
        }

        $order->delete();
        return response()->noContent();
    }

    private function addOrderItems($order, $orderItems) {
        foreach ($orderItems['items'] as $item) {
            $product = Product::query()->find($item['product_id']);

            if (isset($item['price']) && $item['price'] != null) {
                $taxPercent = $product->tax->tax;
                $taxExcludedPrice = ($item['price'] / (($taxPercent / 100) + 1));
                $tax = $taxExcludedPrice * $item['quantity'] * ($taxPercent / 100);
                $subTotal = $taxExcludedPrice * $item['quantity'];
                $itemTotal = $subTotal + $tax;

                OrderItem::query()->create([
                    'quantity' => $item['quantity'],
                    'price' => $taxExcludedPrice,
                    'tax_percent' => $taxPercent,
                    'tax' => $tax,
                    'discount' => 0,
                    'sub_total' => $subTotal,
                    'total' => $itemTotal,
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                ]);
            } else {
                OrderItem::query()->create([
                    'quantity' => $item['quantity'],
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                ]);
            }
        }
    }
}
