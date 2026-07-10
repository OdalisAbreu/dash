<?php

namespace App\Filament\Resources\GoalResource\Pages;

use App\Filament\Resources\GoalResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGoals extends ManageRecords
{
    protected static string $resource = GoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
