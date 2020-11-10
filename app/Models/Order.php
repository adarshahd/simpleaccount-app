<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_type',
        'order_status',
        'customer_id',
        'vendor_id',
    ];

    protected $casts = [
        'customer_id' => 'int',
        'vendor_id' => 'int'
    ];

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }

    public function sale() {
        return $this->hasOne(Sale::class);
    }

    public function purchase() {
        return $this->hasOne(Purchase::class);
    }
}
