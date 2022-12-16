<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomersAccountsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function authenticated_user_can_index_all_customers_accounts()
    {
        $customer = Customer::factory()->create();
        BankAccount::factory(rand(0, 99))->create(['customer_id' => $customer->id]);

        $this->get('api/v1/customers/' . $customer->id . '/accounts', [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee($customer->accounts, false);
    }

    /**
     * @test
     */
    public function authenticated_user_can_see_a_specific_customer_account()
    {
        $customer = Customer::factory()->create();
        $bankAccountId = BankAccount::factory()->create(['customer_id' => $customer->id])->id;

        $this->get('api/v1/customers/' . $customer->id . '/accounts/' . $bankAccountId, [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee($customer->accounts()->first(), false);
    }

    /**
     * @test
     */
    public function authenticated_user_can_create_new_customer_account()
    {
        $customer = Customer::factory()->create();
        $balance = fake()->randomFloat(2, 10, 100);

        $this->post('api/v1/customers/' . $customer->id . '/accounts',compact('balance'), [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ]);

        $this->assertDatabaseCount('bank_accounts', 1)
            ->assertDatabaseHas('bank_accounts', [
                'balance' => $balance,
                'customer_id' => $customer->id
            ]);
    }
}
