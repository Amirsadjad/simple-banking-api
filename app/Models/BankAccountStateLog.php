<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccountStateLog extends Model
{
    use HasFactory;

    protected $fillable = ['account_number', 'transaction_id', 'balance'];

    protected $casts = [
        'balance' => 'float',
    ];
}
