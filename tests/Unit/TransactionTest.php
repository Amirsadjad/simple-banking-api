<?php

namespace Tests\Unit;

use App\Enums\TransactionActionEnum;
use App\Models\BankAccount;
use App\Models\BankAccountStateLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, Transaction::factory()->create()->operator);
    }

    /**
     * @test
     */
    public function it_belongs_to_a_bank_accounts_of_sender_and_receiver()
    {
        $transaction = Transaction::factory()->create(['action'=>TransactionActionEnum::Transfer]);
        $this->assertInstanceOf(BankAccount::class, $transaction->senderAccount()->first());
        $this->assertInstanceOf(BankAccount::class, $transaction->receiverAccount()->first());
    }

    /**
     * @test
     */
    public function it_has_bank_account_state_logs()
    {
        $transaction = Transaction::factory()->create();
        BankAccountStateLog::factory()->create(['transaction_id'=>$transaction->id]);

        $this->assertCount(1, $transaction->accountsState);
    }
}
