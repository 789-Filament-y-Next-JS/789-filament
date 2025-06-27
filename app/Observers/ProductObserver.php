<?php

namespace App\Observers;

use App\Models\Product;
use Filament\Notifications\Notification;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $user = auth()->user();
        dd($user);
        Notification::make()
            ->title('Producto Creado')
            ->body("El producto {$product->name} ha sido creado exitosamente.")
            ->success()
            ->sendToDatabase($user);
            // ->send();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
