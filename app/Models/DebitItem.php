<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $quantity
 * @property float $discount
 * @property int $debit_id
 * @property int $product_stock_id
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class DebitItem extends Model
{
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
        'debit_id',
        'purchase_id',
        'product_stock_id',
    ];

    use HasFactory, SoftDeletes;

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
        'debit_id' => 'integer',
        'product_stock_id' => 'integer',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function debit()
    {
        return $this->belongsTo(\App\Models\Debit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productStock()
    {
        return $this->belongsTo(\App\Models\ProductStock::class);
    }
}
