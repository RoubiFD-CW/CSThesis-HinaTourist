<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        $attendants = [
            ['name' => 'Enchanted', 'email' => 'enchanted@test.com', 'area' => 'Enchanted River'],
            ['name' => 'Lodestone', 'email' => 'lodestone@test.com', 'area' => 'Lodestone Shores Resort'],
            ['name' => 'Baculin', 'email' => 'baculin@test.com', 'area' => 'Baculin Amazing Sand Bar'],
            ['name' => 'Davince', 'email' => 'davince@test.com', 'area' => 'Davince Hidden Paradise'],
            ['name' => 'Harip', 'email' => 'harip@test.com', 'area' => 'Harip Oceanside Beach'],
            ['name' => 'Rock', 'email' => 'rock@test.com', 'area' => 'Rock Island Resort'],
            ['name' => 'Amparitas', 'email' => 'amparitas@test.com', 'area' => 'Amparitas Integrated Nature Farm'],
            ['name' => 'Sibadan', 'email' => 'sibadan@test.com', 'area' => 'Sibadan Fish Cage and Resort'],
            ['name' => 'Hinatuan', 'email' => 'hinatuan@test.com', 'area' => 'Hinatuan Adventure Park'],
            ['name' => 'Mamaon', 'email' => 'mamaon@test.com', 'area' => 'Mamaon Beach Resort'],
            ['name' => 'Landong', 'email' => 'landing@test.com', 'area' => 'Landong Bay'],
            ['name' => 'Tarusan', 'email' => 'tarusan@test.com', 'area' => 'Tarusan Cold Spring'],
            ['name' => 'Llamas', 'email' => 'llamas@test.com', 'area' => 'Llamas Beach Resort'],
            ['name' => 'Brigida', 'email' => 'brigida@test.com', 'area' => 'Puro Brigida’s Beach'],
            ['name' => 'Bunsadan', 'email' => 'bunsadan@test.com', 'area' => 'Bunsadan Falls'],
        ];

        foreach ($attendants as $attendant) {
            User::updateOrCreate(
                ['email' => $attendant['email']],
                [
                    'name' => $attendant['name'],
                    'dedicated_area' => $attendant['area'],
                    'password' => 'password',
                    'is_admin' => false,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Site attendant accounts seeded successfully!');

        $this->call(LogSeeder::class);
    }
}
