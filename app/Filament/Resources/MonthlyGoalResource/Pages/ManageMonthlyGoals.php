<?php

namespace App\Filament\Resources\MonthlyGoalResource\Pages;

use App\Filament\Resources\MonthlyGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMonthlyGoals extends ManageRecords
{
    protected static string $resource = MonthlyGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
