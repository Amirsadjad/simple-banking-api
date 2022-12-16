<?php

namespace Tests\Unit;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_has_bank_accounts()
    {
        $customer = Customer::factory()->create();
        BankAccount::factory()->create(['customer_id' => $customer->id]);
        $this->assertInstanceOf(BankAccount::class, $customer->accounts()->first());
    }

    /**
     * @test
     */
    public function it_can_add_bank_accounts()
    {
        auth()->login(User::factory()->create());
        $customer = Customer::factory()->create();
        $customer->addAccount(['balance' => fake()->randomFloat(2, 10, 100)]);
        $this->assertCount(1, $customer->accounts);
    }
}
