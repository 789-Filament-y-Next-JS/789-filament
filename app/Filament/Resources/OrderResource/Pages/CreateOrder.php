<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Inventory;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array 
    {
        $data['user_id'] = Auth::user()->id;
        return $data;
    }

    // Actualizar el stock de inventario
    protected function afterCreate(): void
    {
        DB::transaction(function (){

            $this->record->load('orderProducts');

            foreach( $this->record->orderProducts as $pivot )
            {
                Inventory::query()
                    ->where('warehouse_id', $this->record->warehouse_id)
                    ->where('product_id', $pivot->product_id)
                    ->decrement('quantity', $pivot->quantity);
            }

        });
    }
}
