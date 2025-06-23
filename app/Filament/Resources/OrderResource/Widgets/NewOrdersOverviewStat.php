<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewOrdersOverviewStat extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            static::getNewOrdersOverviewStat()
        ];
    }

    public static function getNewOrdersOverviewStat()
    {
        $newOrders = Order::whereMonth('created_at', now()->month)->count();
        $beforeOrders = Order::whereMonth('created_at', now()->month - 1)->count();

        $status = ( $newOrders > $beforeOrders ) ? true : false;

        $trendingUp = "heroicon-o-arrow-trending-up";
        $trendingDown = "heroicon-o-arrow-trending-down";


        return Stat::make("Nuevas ventas", $newOrders)
            ->description("Ventas registradas este mes")
            ->descriptionIcon(
                $status ? $trendingUp : $trendingDown
            )
            ->chart([$beforeOrders, $newOrders])
            ->color(
                $status ? "success" : "danger"
            );
    }
}
