<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Salidas';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $slug = "salidas";
    protected static ?string $label = "Salida";
    protected static ?string $pluralLabel = "Salidas";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // INFORMACION DE LA ORDEN
                Section::make('Informacion de la salida')
                    ->schema([
                        Select::make('warehouse_id')
                            ->label('Almacén')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),

                        Select::make('customer_id')
                            ->label('Cliente')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm(
                                CustomerResource::getFormSchema()
                            ),
                        ]),
                
                // CARRITO DE COMPRAS
                Section::make('Carrito de compras')
                    ->schema([
                        Repeater::make('orderProducts')
                            ->relationship()
                            ->columns(3)
                            ->schema([

                                Select::make('product_id')
                                    ->label('Producto')
                                    ->live()
                                    ->relationship('product', 'name')
                                    ->preload()
                                    ->options(
                                        fn(Get $get): array => Product::query()
                                            ->whereHas('inventories', fn($q) => $q->where('warehouse_id', $get('../../warehouse_id')))
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    ),

                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->default(1)
                                    ->reactive(),

                                Placeholder::make('sub_total')
                                    ->label('Subtotal')
                                    ->content(function (Get $get){
                                        $productId = $get('product_id');

                                        $subTotal = $get('quantity') * (Product::find($productId)->price ?? 0);

                                        return number_format($subTotal, 2, ".", "");
                                    })

                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('warehouse_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
