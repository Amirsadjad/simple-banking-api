<?php

namespace Database\Factories;

use App\Enums\TransactionActionEnum;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'operator_id' => User::factory(),
            'action' => fake()->randomElement(TransactionActionEnum::cases()),
            'amount' => fake()->randomFloat(2, 10, 100),
            'sender_account' => fn($attr) => $attr['action']->value === 'deposit' ? null : BankAccount::factory(),
            'receiver_account' => fn($attr) => $attr['action']->value === 'withdrawal' ? null : BankAccount::factory(),
        ];
    }
}
