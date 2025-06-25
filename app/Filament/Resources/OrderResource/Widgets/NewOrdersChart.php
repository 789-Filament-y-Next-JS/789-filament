<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class NewOrdersChart extends ChartWidget
{
    protected array|string|int $columnSpan = 2;

    protected static ?string $heading = 'Ventas mensuales';

    protected function getData(): array
    {

        $now = Carbon::now();
        $yearCurrent = $now->year;

        // Mes Actual
        $monthCurrent = $now->month;
        $startOfCurrent = $now->copy()->startOfMonth();
        $daysInCurrent = $startOfCurrent->daysInMonth;

        // Mes Anterior
        $prev = $now->copy()->subMonth();
        $monthPrev = $prev->month;
        $startOfPrev = $prev->copy()->startOfMonth();
        $daysInPrev = $startOfPrev->daysInMonth;

        // Obtener totales del mes actual y agrupar por día
        $totalsCurrent = Order::selectRaw('DAY(created_at) as day, SUM(total) as total')
            ->whereYear('created_at', $yearCurrent)
            ->whereMonth('created_at', $monthCurrent)
            ->groupByRaw('DAY(created_at)')
            ->pluck('total', 'day');

        // Obtener totales del mes anterior y agrupar por día
        $totalsPrev = Order::selectRaw('DAY(created_at) as day, SUM(total) as total')
            ->whereYear('created_at', $yearCurrent)
            ->whereMonth('created_at', $monthPrev)
            ->groupByRaw('DAY(created_at)')
            ->pluck('total', 'day');
        
        $labels = [];
        $dataCurrent = [];
        $dataPrev = [];

        for( $day = 1; $day <= $daysInCurrent; $day++ )
        {
            $labels[] = sprintf("%02d/%02d", $day, $monthCurrent);

            $dataCurrent[] = isset($totalsCurrent[$day]) ? (float) $totalsCurrent[$day] : 0;

            if( $day <= $daysInPrev )
            {
                $dataPrev[] = isset($totalsPrev[$day]) ? (float) $totalsPrev[$day] : 0;
            }
            else
            {
                $dataPrev[] = 0; // Si el día no existe en el mes anterior, asignar 0
            }

        }

        return [
            "labels" => $labels,
            "datasets" => [
                [
                    "label" => "Mes Actual",
                    "data" => $dataCurrent,
                    "borderColor" => "#4CAF50",
                    "tension" => 0.4,
                ],
                [
                    "label" => "Mes Anterior",
                    "data" => $dataPrev,
                    "tension" => 0.4,
                    "pointBackgroundColor" => "#ffffff",
                ]
            ]
        ];

        

        // $endOfMonth = $now->copy()->endOfMonth();

        // $labels = [];
        // $data = [];

        // for ( $date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay() )
        // {

        //     $labels[] = $date->format("d/m");

        //     $data[] = Order::whereDate('created_at', $date)
        //         ->sum("total");
        // }

        // return [
        //     "datasets" => [
        //         [
        //             "label" => "Ventas $",
        //             "data" => $data,
        //         ],
        //         [
        //             "label" => "Ventas $",
        //             "data" => $data,
        //         ],
        //     ],
        //     "labels" => $labels
        // ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
