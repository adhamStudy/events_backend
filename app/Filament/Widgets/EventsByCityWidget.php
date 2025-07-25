<?php

namespace App\Filament\Widgets;

use App\Models\City;
use App\Models\Event;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EventsByCityWidget extends ChartWidget
{
    protected static ?string $heading = 'Events by City';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $data = City::withCount('events')
            ->orderBy('events_count', 'desc')
            ->limit(10) // Show top 10 cities
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Events',
                    'data' => $data->pluck('events_count')->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#FF6384',
                        '#C9CBCF',
                        '#4BC0C0',
                        '#36A2EB',
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'aspectRatio' => 1,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
