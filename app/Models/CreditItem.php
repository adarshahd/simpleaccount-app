<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $quantity
 * @property float $discount
 * @property int $credit_id
 * @property int $product_stock_id
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CreditItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity',
        'price',
        'discount',
        'tax_percent',
        'tax',
        'sub_total',
        'total',
        'credit_id',
        'sale_id',
        'product_stock_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'quantity' => 'integer',
        'price' => 'float',
        'discount' => 'float',
        'tax_percent' => 'float',
        'tax' => 'float',
        'sub_total' => 'float',
        'total' => 'float',
        'credit_id' => 'integer',
        'product_stock_id' => 'integer',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function credit()
    {
        return $this->belongsTo(\App\Models\Credit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productStock()
    {
        return $this->belongsTo(\App\Models\ProductStock::class);
    }
}
