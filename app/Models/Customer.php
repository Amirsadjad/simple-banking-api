<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['name'];

    /**
     * @return HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * @param array $balance
     * @return BankAccount
     */
    public function addAccount(array $balance): BankAccount
    {
        return $this->accounts()->create($balance + ['operator_id', auth()->user()->id]);
    }
}
