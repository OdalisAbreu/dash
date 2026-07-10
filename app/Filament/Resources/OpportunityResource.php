<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Models\Opportunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $modelLabel = 'Oportunidad';

    protected static ?string $pluralModelLabel = 'Oportunidades';

    protected static ?string $navigationGroup = 'Datos comerciales';

    public static function months(): array
    {
        return [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
            0 => 'Otros / sin mes específico',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del cliente')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('service')
                    ->label('Servicio / Programa')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('category')
                    ->label('Variable')
                    ->options(Opportunity::CATEGORIES)
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Select::make('month')
                    ->label('Mes')
                    ->options(self::months())
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->label('Año')
                    ->required()
                    ->numeric()
                    ->default(now()->year),
                Forms\Components\TextInput::make('status')
                    ->label('Estado / Nota')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('pending_invoice')
                    ->label('Ganado, pendiente por facturar')
                    ->helperText('Actívalo si esta oportunidad ya se ganó pero todavía no se ha facturado.')
                    ->visible(fn (Forms\Get $get) => $get('category') === 'facturado' || $get('category') === 'pipeline')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service')
                    ->label('Servicio')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('category')
                    ->label('Variable')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Opportunity::CATEGORIES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'facturado' => 'success',
                        'perdido' => 'danger',
                        'pipeline' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                    ->label('Mes')
                    ->formatStateUsing(fn (int $state) => self::months()[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('pending_invoice')
                    ->label('Pend. facturar')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Variable')
                    ->options(Opportunity::CATEGORIES),
                Tables\Filters\SelectFilter::make('month')
                    ->label('Mes')
                    ->options(self::months()),
                Tables\Filters\SelectFilter::make('year')
                    ->label('Año')
                    ->options(fn () => \App\Models\Opportunity::query()->distinct()->orderByDesc('year')->pluck('year', 'year')),
                Tables\Filters\TernaryFilter::make('pending_invoice')
                    ->label('Pendiente por facturar'),
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
            'index' => Pages\ManageOpportunities::route('/'),
        ];
    }
}
