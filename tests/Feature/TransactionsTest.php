<?php

namespace Tests\Feature;

use App\Enums\TransactionActionEnum;
use App\Models\BankAccount;
use App\Models\BankAccountStateLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function authenticated_user_can_see_a_specific_transactions_and_all_its_states()
    {
        $transaction = Transaction::factory()->create();
        BankAccountStateLog::factory(rand(1, 2))->create([
            'transaction_id' => $transaction->id
        ]);

        $this->get('api/v1/transactions/' . $transaction->id, [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee(Transaction::first(), false)
            ->assertSee($transaction->accountsState, false);
    }

    /**
     * @test
     */
    public function authenticated_user_can_make_a_deposit_transaction()
    {
        $receiverAccount = BankAccount::factory()->create();
        $action = TransactionActionEnum::Deposit;
        $amount = fake()->randomFloat(2, 10, 100);

        $this->post('api/v1/transactions/deposit', [
            'amount' => $amount,
            'account_number' => $receiverAccount->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ]);

        $this->assertDatabaseHas('transactions', [
                'amount' => $amount,
                'action' => $action,
                'receiver_account' => $receiverAccount->id
            ]
        )->assertDatabaseCount('transactions', 1);
    }

    /**
     * @test
     */
    public function authenticated_user_can_make_a_withdrawal_transaction()
    {
        $senderAccount = BankAccount::factory()->create();
        $amount = fake()->randomFloat(2, 10, $senderAccount->balance - 1);

        $this->post('api/v1/transactions/withdraw', [
            'amount' => $amount,
            'account_number' => $senderAccount->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ]);

        $this->assertDatabaseHas('transactions', [
                'amount' => $amount,
                'action' => TransactionActionEnum::Withdrawal,
                'sender_account' => $senderAccount->id
            ]
        )->assertDatabaseCount('transactions', 1);
    }

    /**
     * @test
     */
    public function authenticated_user_can_make_a_transfer_transaction()
    {
        $senderAccount = BankAccount::factory()->create();
        $receiverAccount = BankAccount::factory()->create();
        $amount = fake()->randomFloat(2, 10, $senderAccount->balance - 1);

        $this->post('api/v1/transactions/transfer', [
            'amount' => $amount,
            'sender_account_number' => $senderAccount->id,
            'receiver_account_number' => $receiverAccount->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ]);

        $this->assertDatabaseHas('transactions', [
                'amount' => $amount,
                'action' => TransactionActionEnum::Transfer,
                'sender_account' => $senderAccount->id,
                'receiver_account' => $receiverAccount->id
            ]
        )->assertDatabaseCount('transactions', 1);
    }
}
