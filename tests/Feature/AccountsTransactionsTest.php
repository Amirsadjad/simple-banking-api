<?php

namespace Tests\Feature;

use App\Enums\TransactionActionEnum;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountsTransactionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function authenticated_user_can_index_all_accounts_transactions()
    {
        $account = BankAccount::factory()->create();
        Transaction::factory(rand(0, 99))->create([
            'action' => TransactionActionEnum::Deposit,
            'receiver_account' => $account->id
        ]);

        $this->get('api/v1/accounts/' . $account->id . '/transactions', [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee($account->transactions, false);
    }

    /**
     * @test
     */
    public function authenticated_user_can_see_a_specific_account_transaction()
    {
        $account = BankAccount::factory()->create();
        $transactionId = Transaction::factory()->create([
            'action' => TransactionActionEnum::Deposit,
            'receiver_account' => $account->id
        ])->id;

        $this->get('api/v1/accounts/' . $account->id . '/transactions/' . $transactionId, [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee($account->transactions()->first(), false);
    }
}
