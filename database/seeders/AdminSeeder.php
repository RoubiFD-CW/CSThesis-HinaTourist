<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
        ['email' => 'admin@lgu.com'],
        [
            'name' => 'Admin',
            'email' => 'admin@lgu.com',
            'password' => 'Admin@1234',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]
        );

        $this->command->info('✅ Admin user seeded successfully!');
        $this->command->info('   Email:    admin@lgu.com');
        $this->command->info('   Password: Admin@1234');
    }
}
