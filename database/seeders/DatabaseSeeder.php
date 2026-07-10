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
        User::firstOrCreate(
            ['email' => 'odalisdabreu@gmail.com'],
            [
                'name' => 'Odalis Abreu',
                'password' => bcrypt('Dashboard2026!'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'katherinecornielle07@gmail.com'],
            [
                'name' => 'Katherine Cornielle',
                'password' => bcrypt('Katherine2026'),
            ]
        );

        $this->call(CommercialDataSeeder::class);
        $this->call(MonthlyGoalSeeder::class);
    }
}
