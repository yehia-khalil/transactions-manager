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
        $users = User::factory(10)->create();
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
        ]);

        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'admin'
        ]);
        $userRole = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'user'
        ]);
        $userRole->users()->attach($users);
        $admin->assignRole($adminRole);
        // $this->call([TransactionSeeder::class]);
    }
}
