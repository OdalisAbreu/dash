<?php

namespace Database\Seeders;

use App\Models\MonthlyGoal;
use Illuminate\Database\Seeder;

class MonthlyGoalSeeder extends Seeder
{
    private const AMOUNTS = [
        1 => 19430,
        2 => 39800,
        3 => 41000,
        4 => 23000,
        5 => 24000,
        6 => 25000,
        7 => 22000,
        8 => 17000,
        9 => 14000,
        10 => 9000,
        11 => 13500,
        12 => 2270,
    ];

    public function run(): void
    {
        foreach (self::AMOUNTS as $month => $amount) {
            MonthlyGoal::updateOrCreate(
                ['year' => 2026, 'month' => $month],
                ['amount' => $amount]
            );
        }
    }
}
