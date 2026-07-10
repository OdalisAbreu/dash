<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoalResource\Pages;
use App\Models\Goal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GoalResource extends Resource
{
    protected static ?string $model = Goal::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $modelLabel = 'Meta anual';

    protected static ?string $pluralModelLabel = 'Metas anuales';

    protected static ?string $navigationGroup = 'Datos comerciales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year')
                    ->label('Año')
                    ->required()
                    ->numeric()
                    ->default(now()->year)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('amount')
                    ->label('Meta ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Meta')
                    ->money('USD')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGoals::route('/'),
        ];
    }
}
