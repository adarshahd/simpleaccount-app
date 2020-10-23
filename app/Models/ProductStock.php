<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $identification
 * @property int $stock
 * @property int $total_stock
 * @property float $mrp
 * @property float $price
 * @property float $tax
 * @property string $hsn
 * @property string $batch
 * @property \Carbon\Carbon $expiry
 * @property int $product_id
 * @property int $manufacturer_id
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProductStock extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identification',
        'stock',
        'total_stock',
        'mrp',
        'price',
        'tax',
        'hsn',
        'batch',
        'expiry',
        'product_id',
        'manufacturer_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'stock' => 'integer',
        'total_stock' => 'integer',
        'mrp' => 'float',
        'price' => 'float',
        'tax' => 'float',
        'product_id' => 'integer',
        'manufacturer_id' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expiry',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo(\App\Models\Manufacturer::class);
    }
}
