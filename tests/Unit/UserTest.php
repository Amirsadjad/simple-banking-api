<?php

namespace Tests\Unit;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_has_bank_accounts()
    {
        $user = User::factory()->create();
        BankAccount::factory()->create(['operator_id' => $user->id]);
        $this->assertInstanceOf(BankAccount::class, $user->customerAccounts()->first());
    }

    /**
     * @test
     */
    public function it_has_transactions()
    {
        $user = User::factory()->create();
        Transaction::factory()->create(['operator_id' => $user->id]);
        $this->assertInstanceOf(Transaction::class, $user->accountTransactions()->first());
    }

    /**
     * @test
     */
    public function it_can_make_transactions()
    {
        $user = User::factory()->create();
        $user->makeTransaction(
            collect(Transaction::factory()->raw(['operator_id' => null]))->except('operator_id')->toArray()
        );
        $this->assertCount(1, $user->accountTransactions);
    }
}
