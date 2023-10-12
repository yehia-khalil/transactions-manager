<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\TransactionStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
        ]);
        User::factory(10)->create();

        $role = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'admin'
        ]);
        $admin->assignRole($role);
        TransactionStatus::insert([
            [
                'name' => 'Paid'
            ],
            [
                'name' => 'Outstanding'
            ],
            [
                'name' => 'Overdue'
            ]
        ]);
    }
}
