<?php

namespace App\Models;

use Cassandra\Custom;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transactions extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['operator_id', 'sender_account', 'receiver_account', 'action', 'amount'];

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
    public function senderAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'sender_account');
    }

    /**
     * @return BelongsTo
     */
    public function receiverAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'receiver_account');
    }
}
