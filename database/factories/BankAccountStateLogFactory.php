<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankAccountStateLog>
 */
class BankAccountStateLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'account_number' => BankAccount::factory(),
            'transaction_id' => Transaction::factory(),
            'balance' => fake()->randomFloat(2, 10, 1000)
        ];
    }
}
