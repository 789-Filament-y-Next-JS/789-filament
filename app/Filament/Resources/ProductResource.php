<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Menu principal';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    
    protected static ?string $slug = "productos";
    protected static ?string $label = "Producto";
    protected static ?string $pluralLabel = "Productos";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informacion del producto')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('¿Esta activo?')
                            ->default(true)
                            ->columnSpan(3),

                        TextInput::make('code')
                            ->label('Codigo')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('Ej: FIC-0001'),
                            
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),

                        TextInput::make('summary')
                            ->label('Resumen')
                            ->required(),

                        TextInput::make('price')
                            ->label('Precio de venta')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->prefix('$'),

                        Select::make('category_id')
                            ->label('Categoria')
                            ->required()
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                    ]),
                Section::make('Imagen del producto')
                    ->schema([
                        FileUpload::make('image')
                            ->disk('public')
                            ->label('Imagen')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/*'])
                            ->required()
                    ]),

                Section::make('Descripción detallada')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Descripción')
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Codigo'),

                ImageColumn::make('image')
                    ->size(50)
                    ->label('Codigo'),

                TextColumn::make('name')
                    ->label('Nombre'),

                TextColumn::make('summary')
                    ->label('Codigo'),

                TextColumn::make('created_at')
                    ->label('Estado'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
