<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function authenticated_user_can_index_all_customers()
    {
        Customer::factory(rand(0, 99))->create();

        $this->get('api/v1/customers', [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee(Customer::all(), false);
    }

    /**
     * @test
     */
    public function authenticated_user_can_see_a_specific_customer()
    {
        $this->get('api/v1/customers/' . Customer::factory()->create()->id, [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ])->assertSee(Customer::first(), false);
    }

    /**
     * @test
     */
    public function authenticated_user_can_create_new_customers()
    {
        $name = fake()->name;

        $this->post('api/v1/customers',compact('name'), [
            'Accept' => 'application/json',
            'Authorization' => 'bearer' . auth()->login(User::factory()->create())
        ]);

        $this->assertDatabaseCount('customers', 1)
            ->assertDatabaseHas('customers', compact('name'));
    }
}
