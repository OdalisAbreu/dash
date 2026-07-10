<?php

namespace App\Filament\Resources\MonthlyStatResource\Pages;

use App\Filament\Resources\MonthlyStatResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMonthlyStats extends ManageRecords
{
    protected static string $resource = MonthlyStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
