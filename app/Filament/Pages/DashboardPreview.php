<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DashboardPreview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'Vista previa del dash';

    protected static ?string $title = 'Vista previa del dashboard';

    protected static ?string $navigationGroup = 'Datos comerciales';

    protected static ?int $navigationSort = -1;

    protected static string $view = 'filament.pages.dashboard-preview';

    public function getDashboardUrl(): string
    {
        return route('dashboard');
    }
}
