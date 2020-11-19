<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bank_name',
        'bank_branch',
        'account_name',
        'account_number',
        'account_type',
        'ifsc',
        'balance',
        'active'
    ];

    protected $casts = [
        'balance' => 'float',
        'active' => 'boolean',
    ];

    public function transactions() {
        return $this->hasMany(AccountTransaction::class);
    }
}
