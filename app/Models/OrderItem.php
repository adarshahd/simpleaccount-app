<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'price',
        'tax_percent',
        'tax',
        'discount',
        'sub_total',
        'total',
        'product_id',
        'order_id',
    ];

    protected $casts = [
        'quantity' => 'int',
        'price' => 'float',
        'tax_percent' => 'float',
        'tax' => 'float',
        'discount' => 'float',
        'sub_total' => 'float',
        'total' => 'float',
        'product_id' => 'int',
        'order_id' => 'int',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
