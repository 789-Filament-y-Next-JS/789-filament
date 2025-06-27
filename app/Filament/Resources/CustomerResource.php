<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $slug = "clientes";
    protected static ?string $label = "Cliente";
    protected static ?string $pluralLabel = "Clientes";

    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Información del cliente')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Estado del cliente')
                        ->required()
                        ->columnSpan(2)
                        ->default(true),

                    Forms\Components\TextInput::make('name')
                        ->label('Nombre completo')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Correo electrónico')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label('Teléfono')
                        ->tel()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('nit')
                        ->label('Direccion de facturación')
                        ->required()
                        ->maxLength(255)
                        ->unique(table: "customers", column: 'nit', ignorable: fn($record) => $record)
                        ->rules([
                            "unique:customers,nit"
                        ])
                        ->validationMessages([
                            'unique' => 'El NIT ya está en uso.',
                        ]),

                    Forms\Components\TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->maxLength(255),
                ])
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getFormSchema());
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make()
                    ->columns([
                        'sm' => 1,
                        "md" => 4,
                        "lg" => 5,
                    ])
                    ->schema([
                        Section::make('Informacion del cliente')
                            ->columnSpan([
                                'sm' => 1,
                                "md" => 4,
                                "lg" => 3,
                            ])
                            ->columns(2)
                            ->grow()
                            ->schema([
                                TextEntry::make('name')
                                    // ->weight(FontWeight::Bold)
                                    ->label('Nombre completo'),

                                TextEntry::make('email')
                                    ->label('Correo electrónico'),


                                TextEntry::make('phone')
                                    ->label('Teléfono'),


                                TextEntry::make('nit')
                                    ->label('Documento de facturación'),


                            ]),
                        Section::make('Información adicional')
                            ->columns(2)
                            ->columnSpan([
                                'sm' => 1,
                                "md" => 4,
                                "lg" => 2,
                            ])
                            ->schema([
                                TextEntry::make('is_active')
                                    ->label('Estado')
                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Activo' : 'Inactivo'),
                                TextEntry::make('created_at')
                                    ->label('Creado el')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Actualizado el')
                                    ->dateTime()
                            ]),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nit')
                    ->searchable(),

                TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Activo' : 'Inactivo'),

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
                Tables\Actions\ViewAction::make(),
                // ->slideOver(),
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
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }
}
