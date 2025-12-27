<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user with complete profile
        $admin = User::create([
            'name'              => 'Admin User',
            'email'             => 'admin@example.com',
            'password'          => bcrypt('password'),
            'is_admin'          => true,
            'email_verified_at' => now(),
        ]);

        // Complete admin profile
        $admin->profile->update([
            'city'                => 'Roma',
            'address'             => 'Via del Corso',
            'house_number'        => '100',
            'postal_code'         => '00100',
            'max_guests'          => 6,
            'privacy_accepted_at' => now(),
        ]);

        $this->command->info('✅ Admin user created with profile');
    }
}
