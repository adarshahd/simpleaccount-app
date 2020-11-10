<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $bill_id
 * @property string $bill_number
 * @property \Carbon\Carbon $bill_date
 * @property float $sub_total
 * @property float $discount
 * @property float $tax
 * @property float $total
 * @property int $customer_id
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Sale extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bill_id',
        'bill_number',
        'bill_date',
        'sub_total',
        'discount',
        'tax',
        'total',
        'customer_id',
        'order_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'bill_id' => 'integer',
        'sub_total' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'total' => 'float',
        'customer_id' => 'integer',
        'order_id' => 'integer'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'bill_date',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleItems()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }

    public function order() {
        $this->belongsTo(Order::class);
    }
}
