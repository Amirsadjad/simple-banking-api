<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['operator_id', 'customer_id', 'balance'];

    protected $casts = [
        'balance' => 'float'
    ];

    /**
     * @return HasOne
     */
    public function operator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany
     */
    public function stateLogs(): HasMany
    {
        return $this->hasMany(BankAccountStateLog::class, 'account_number');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'sender_account')
            ->orWhere('receiver_account', $this->getAttribute('id'));
    }

    /**
     * @param string $transactionId
     * @return BankAccount
     */
    public function logState(string $transactionId): BankAccount
    {
        $this->stateLog()->create([
            'transaction_id' => $transactionId,
            'account_number' => $this->getAttribute('account_number'),
            'balance' => $this->getAttribute('balance')
        ]);

        return $this;
    }
}
