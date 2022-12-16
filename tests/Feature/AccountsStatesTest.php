<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\BankAccountStateLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountsStatesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function authenticated_user_can_index_all_accounts_states()
    {
        $account = BankAccount::factory()->create();
        BankAccountStateLog::factory(rand(0, 99))->create([
            'account_number' => $account->id
        ]);

        $this->get('api/v1/accounts/' . $account->id . '/states', [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee($account->stateLogs, false);
    }

    /**
     * @test
     */
    public function authenticated_user_can_see_a_specific_account_state()
    {
        $account = BankAccount::factory()->create();
        $stateId = BankAccountStateLog::factory()->create([
            'account_number' => $account->id
        ])->id;

        $this->get('api/v1/accounts/' . $account->id . '/states/' . $stateId, [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee($account->stateLogs()->first(), false);
    }
}
