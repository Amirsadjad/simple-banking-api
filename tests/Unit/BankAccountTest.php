<?php

namespace Tests\Unit;

use App\Models\BankAccount;
use App\Models\BankAccountStateLog;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, BankAccount::factory()->create()->operator);
    }

    /**
     * @test
     */
    public function it_belongs_to_a_customer()
    {
        $this->assertInstanceOf(Customer::class, BankAccount::factory()->create()->customer);
    }

    /**
     * @test
     */
    public function it_has_transactions_as_sender_or_receiver_account()
    {
       $account = BankAccount::factory()->create();
       Transaction::factory()->create(['sender_account' => $account->id]);
       Transaction::factory()->create(['receiver_account' => $account->id]);

       $account->transactions()->each(
           fn($transaction) => $this->assertInstanceOf(Transaction::class, $transaction)
       );

       $this->assertCount(2, $account->transactions);
    }

    /**
     * @test
     */
    public function it_has_bank_account_state_logs()
    {
        $account = BankAccount::factory()->create();
        BankAccountStateLog::factory()->create(['account_number' => $account->id]);

        $this->assertInstanceOf(BankAccountStateLog::class, $account->stateLogs()->first());
    }

    /**
     * @test
     */
    public function it_can_create_bank_account_state_logs()
    {
        $account = BankAccount::factory()->create();
        $account->logState(Transaction::factory()->create()->id);

        $this->assertCount(1, $account->stateLogs);
    }
}
