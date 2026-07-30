<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 0.5, 5),
            'type' => TransactionType::TaskReward,
            'description' => 'Task reward',
        ];
    }

    public function debit(float $amount): static
    {
        return $this->state(fn () => [
            'amount' => -abs($amount),
            'type' => TransactionType::Withdrawal,
            'description' => 'Payout request',
        ]);
    }
}
