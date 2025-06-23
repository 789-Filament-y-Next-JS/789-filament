<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewCustomersOverview extends BaseWidget
{
    protected static bool $isLazy = true;

    protected static ?string $pollingInterval = "5s";

    protected function getStats(): array
    {
        return [
            static::getNewCustomersOverviewStat()
        ];
    }


    public static function getNewCustomersOverviewStat()
    {

        $newCustomers = Customer::whereMonth('created_at', now()->month )->count();
        $beforeCustomers = Customer::whereMonth('created_at', now()->month - 1)->count();

        return Stat::make("Nuevos clientes", $newCustomers)
            ->description("Clientes registrados este mes")
            ->chart([$beforeCustomers, $newCustomers])
            ->color("primary");
    }
}
