<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CustomerResource\Widgets\NewCustomersOverview;
use App\Filament\Resources\OrderResource\Widgets\NewOrdersOverviewStat;
use App\Filament\Resources\ProductResource\Widgets\ProductOverview;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            ProductOverview::getProductOverviewStat(),
            NewCustomersOverview::getNewCustomersOverviewStat(),
            NewOrdersOverviewStat::getNewOrdersOverviewStat(),
        ];
    }
}
