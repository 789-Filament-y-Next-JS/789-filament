<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class NewOrdersChart extends ChartWidget
{
    protected array|string|int $columnSpan = 2;

    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {

        $now = Carbon::now();

        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $labels = [];
        $data = [];

        for ( $date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay() )
        {

            $labels[] = $date->format("d/m");

            $data[] = Order::whereDate('created_at', $date)
                ->sum("total");
        }

        return [
            "datasets" => [
                [
                    "label" => "Ventas $",
                    "data" => $data,
                ],
                [
                    "label" => "Ventas $",
                    "data" => $data,
                ],
            ],
            "labels" => $labels
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
