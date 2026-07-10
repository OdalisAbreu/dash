<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyGoalResource\Pages;
use App\Models\MonthlyGoal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class MonthlyGoalResource extends Resource
{
    protected static ?string $model = MonthlyGoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Meta mensual';

    protected static ?string $pluralModelLabel = 'Metas mensuales';

    protected static ?string $navigationGroup = 'Datos comerciales';

    public static function months(): array
    {
        return [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('month')
                    ->label('Mes')
                    ->options(self::months())
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Forms\Get $get) => $rule->where('year', $get('year')),
                    ),
                Forms\Components\TextInput::make('year')
                    ->label('Año')
                    ->required()
                    ->numeric()
                    ->default(now()->year),
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
            ->defaultSort('month')
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Mes')
                    ->formatStateUsing(fn (int $state) => self::months()[$state] ?? $state)
                    ->sortable(),
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
            'index' => Pages\ManageMonthlyGoals::route('/'),
        ];
    }
}
