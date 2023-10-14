<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionPayment;
use App\Models\TransactionSubCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TransactionCategory::factory()
            ->count(2)
            ->create()
            ->each(function ($transactionCategory) {
                TransactionSubCategory::factory()->count(2)->create([
                    'transaction_category_id' => $transactionCategory->id
                ]);
            });
        $category = TransactionCategory::first();
        $currentDate = Carbon::now()->addYears(2);
        $startYear = $currentDate->year - 5;
        for ($year = $startYear; $year <= $currentDate->year; $year++) {
            // Loop for transactions every 4 months
            for ($month = 1; $month <= 12; $month += 4) {
                $dueDate = Carbon::create($year, $month, 1);
                $transaction = Transaction::factory()->create([
                    'due_date' => $dueDate,
                    'payer' => (User::first())->id,
                    'transaction_category_id' => $category->id,
                    'amount' => 1000
                ]);
                $rand = rand(1, 100);
                if ($rand > 50) {
                    TransactionPayment::factory()->create([
                        'transaction_id' => $transaction->id,
                        'amount' => 1000
                    ]);
                }
            }
        }
    }
}
