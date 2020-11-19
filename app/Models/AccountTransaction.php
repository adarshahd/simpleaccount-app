<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'total',
        'method',
        'reference',
        'notes',
        'date',
        'account_id'
    ];

    protected $casts = [
        'total' => 'float',
        'account_id' => 'integer'
    ];

    protected $dates = [
        'date'
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    }
}
