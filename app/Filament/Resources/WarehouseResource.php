<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationGroup = 'Menu principal';
    protected static ?string $navigationLabel = 'Almacenes';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $slug = "almacenes";
    protected static ?string $label = "Almacén";
    protected static ?string $pluralLabel = "Almacenes";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Almacén')
                    ->columns(2)
                    ->schema(
                        static::getFormSchema()
                    )
            ]);
    }

    public static function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Nombre del Almacén')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('address')
                ->label('Dirección del Almacén')
                ->required()
                ->maxLength(255),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('F. creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('F. actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
