<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();
        \App\Models\User::create([
            'name' => 'Brother Admin',
            'user_name' => 'admin_admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'user_role' => 'admin',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'registered_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        \App\Models\User::create([
            'name' => 'User',
            'user_name' => 'user_user',
            'email' => 'user@user.com',
            'email_verified_at' => now(),
            'user_role' => 'user',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'registered_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
