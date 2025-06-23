<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            static::getProductOverviewStat()        
        ];
    }
    
    public static function getProductOverviewStat()
    {
        $totalProducts = Product::count();

        return Stat::make('Productos', $totalProducts)
                ->description('Productos registrados')
                ->color('primary')
                ->chart([7,12,3,4,0]);
    }
}
