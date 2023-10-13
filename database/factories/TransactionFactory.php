<?php

namespace Database\Factories;

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
    public function definition(): array
    {
        return [
            'transaction_category_id' => \App\Models\TransactionCategory::factory(),
            // 'transaction_sub_category_id' => \App\Models\TransactionSubCategory::factory(),
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'payer' => \App\Models\User::factory(),
            'due_date' => $this->faker->date,
            'vat' => $this->faker->numberBetween(10, 15),
            'is_vat_inclusive' => $this->faker->boolean,
        ];
    }
}
