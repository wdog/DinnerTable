<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user (no profile needed)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create regular users with complete profiles
        $mario = User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);
        $mario->profile->update([
            'city' => 'Roma',
            'address' => 'Via Roma 10',
            'max_guests' => 6,
            'privacy_accepted_at' => now(),
        ]);

        $giulia = User::create([
            'name' => 'Giulia Bianchi',
            'email' => 'giulia@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);
        $giulia->profile->update([
            'city' => 'Milano',
            'address' => 'Via Milano 25',
            'max_guests' => 4,
            'privacy_accepted_at' => now(),
        ]);
    }
}
