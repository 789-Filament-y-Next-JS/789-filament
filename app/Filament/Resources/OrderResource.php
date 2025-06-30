<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
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
                        TextInput::make('note')
                            ->label('Nota adicional')
                    ]),

                // CARRITO DE COMPRAS
                Section::make('Carrito de compras')
                    ->hidden(
                        fn(Get $get): bool => empty($get('warehouse_id'))
                    )
                    ->schema([
                        Repeater::make('orderProducts')
                            ->relationship()
                            ->columns(3)
                            ->extraAttributes([
                                // "wire:poll.500ms" => "",
                                "wire:poll.visible" => ""
                            ])
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
                                    ->reactive()
                                    ->rule(function (Get $get){
                                        
                                        $productId = $get('product_id');
                                        $warehouseId = $get('../../warehouse_id');

                                        $stock = Inventory::where('product_id', $productId)
                                            ->where('warehouse_id', $warehouseId)
                                            ->value('quantity') ?? 0;

                                        return "max:$stock";

                                    })
                                    ->validationMessages([
                                        "max" => "No hay stock suficiente",
                                    ])
                                    ->helperText(function (Get $get) {
                                        $productId = $get('product_id');
                                        $warehouseId = $get('../../warehouse_id');

                                        $stock = Inventory::where('product_id', $productId)
                                            ->where('warehouse_id', $warehouseId)
                                            ->value('quantity') ?? 0;

                                        return "Stock disponible $stock";
                                    }),

                                Placeholder::make('sub_total')
                                    ->label('Subtotal')
                                    ->content(function (Get $get) {
                                        $productId = $get('product_id');
                                        $quantity = $get('quantity') ?? 0;
                                        $productPrice = Product::find($productId)->price ?? 0;

                                        $subTotal = (float) $quantity * (float) $productPrice;

                                        return number_format($subTotal, 2, ".", "");
                                    })
                            ])
                            ->afterStateUpdated(function ($set, $state) {
                                $total = 0;

                                foreach ($state as $item) {
                                    $productId = $item['product_id'];
                                    $quantity = $item['quantity'] ?? 0;

                                    $product = Product::find($productId);

                                    $total += (float) $quantity * (float) ($product->price ?? 0);
                                }

                                $set('total', $total);
                            })
                            
                            // ACTUALIZAR SUB TOTAL
                            ->mutateRelationshipDataBeforeCreateUsing(function(array $data): array {
                                $productId = $data['product_id'];
                                $quantity = $data['quantity'] ?? 0;

                                $product = Product::find($productId);

                                $data["sub_total"] = $quantity * $product->price;

                                return $data;
                            }),
                    ]),
                Section::make('Totales a pagar')
                    ->schema([
                        Hidden::make('total')
                            ->dehydrated()
                            ->live(),


                        Placeholder::make('')
                            ->label('Total a pagar')
                            ->columnSpan(2)
                            ->reactive()
                            ->content(function (Get $get) {
                                $total = $get('total');
                                return number_format($total, 2, '.', '');
                            })

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    // ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
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
            'view' => Pages\EditOrder::route('/{record}'),
        ];
    }
}
