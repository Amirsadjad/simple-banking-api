<?php

namespace Tests\Feature;

use App\Enums\UserRoleEnum;
use App\Models\BankAccount;
use App\Models\BankAccountStateLog;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function any_user_can_login()
    {
        $user = User::factory()->create();

        $this->post('api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ])->assertSee('success');
    }

    /**
     * @test
     */
    public function only_admin_can_register_new_users()
    {
        $adminUser = User::factory()->create(['role' => UserRoleEnum::Admin]);
        $nonAdminUser = User::factory()->create();

        $this->post('api/v1/auth/register', [
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail(),
            'password' => '@Aa1234567'
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'bearer ' . auth()->login($adminUser)
        ])->assertSee('success');

        $this->post('api/v1/auth/register', [
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail(),
            'password' => '@Aa1234567'
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'bearer ' . auth()->login($nonAdminUser)
        ])->assertSee('Unauthorized');
    }

    /**
     * @test
     */
    public function only_authenticated_users_can_access_banking_endpoints()
    {
        $cId = Customer::factory()->create()->id;
        $baId = BankAccount::factory()->create()->id;
        $baslId = BankAccountStateLog::factory()->create()->id;
        $tId = Transaction::factory()->create()->id;

        $this->get('api/v1/customers')->assertSee('Unauthorized');
        $this->get('api/v1/customers/'.$cId)->assertSee('Unauthorized');
        $this->post('api/v1/customers', [])->assertSee('Unauthorized');

        $this->get('api/v1/customers/'.$cId.'/accounts')->assertSee('Unauthorized');
        $this->get('api/v1/customers/'.$cId.'/accounts/'.$baId)->assertSee('Unauthorized');
        $this->post('api/v1/customers/'.$cId.'/accounts', [])->assertSee('Unauthorized');

        $this->get('api/v1/accounts/'.$baId.'/transactions')->assertSee('Unauthorized');
        $this->get('api/v1/accounts/'.$baId.'/transactions/'.$tId)->assertSee('Unauthorized');

        $this->get('api/v1/accounts/'.$baId.'/states')->assertSee('Unauthorized');
        $this->get('api/v1/accounts/'.$baId.'/states/'.$tId)->assertSee('Unauthorized');

        $this->get('api/v1/transactions/'.$tId)->assertSee('Unauthorized');
        $this->post('api/v1/transactions/deposit', [])->assertSee('Unauthorized');
        $this->post('api/v1/transactions/withdraw', [])->assertSee('Unauthorized');
        $this->post('api/v1/transactions/transfer', [])->assertSee('Unauthorized');
    }
}
