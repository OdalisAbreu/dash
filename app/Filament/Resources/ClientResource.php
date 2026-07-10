<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use App\Models\Opportunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationGroup = 'Datos comerciales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Repeater::make('opportunities')
                    ->relationship()
                    ->label('Oportunidades')
                    ->schema([
                        Forms\Components\TextInput::make('service')
                            ->label('Servicio / Programa')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('category')
                            ->label('Variable')
                            ->options(Opportunity::CATEGORIES)
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('month')
                            ->label('Mes')
                            ->options(OpportunityResource::months())
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
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->itemLabel(fn (array $state): ?string => $state['service'] ?? 'Nueva oportunidad')
                    ->collapsible()
                    ->addActionLabel('Agregar oportunidad')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('opportunities_count')
                    ->label('Oportunidades')
                    ->counts('opportunities'),
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
            'index' => Pages\ManageClients::route('/'),
        ];
    }
}
