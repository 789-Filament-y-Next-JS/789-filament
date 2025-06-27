<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('orders')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Compras del cliente')
            ->columns([
                // Tables\Columns\TextColumn::make('orders'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label("Vendedor")
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label("Almacén")
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label("Total")
                    ->sortable()
                    ->searchable()
                    ->money('usd', true)
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->url(fn($record) => OrderResource::getUrl('view', ["record" => $record->id]))
                    ->icon('heroicon-o-eye')

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
