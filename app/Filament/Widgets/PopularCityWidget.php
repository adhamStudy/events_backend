<?php

namespace App\Filament\Widgets;

use App\Models\City;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PopularCityWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $topCity = City::withCount('events')
            ->orderBy('events_count', 'desc')
            ->first();

        $totalCities = City::count();
        $citiesWithEvents = City::has('events')->count();

        return [
            Stat::make('Most Popular City', $topCity ? $topCity->name : 'No events yet')
                ->description($topCity ? "{$topCity->events_count} events" : 'Create some events first')
                ->descriptionIcon($topCity ? 'heroicon-m-map-pin' : 'heroicon-m-exclamation-triangle')
                ->color($topCity ? 'success' : 'warning'),

            Stat::make('Active Cities', $citiesWithEvents)
                ->description("Out of {$totalCities} total cities")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Cities Coverage', $totalCities > 0 ? round(($citiesWithEvents / $totalCities) * 100, 1) . '%' : '0%')
                ->description('Cities with events')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($totalCities > 0 && ($citiesWithEvents / $totalCities) > 0.5 ? 'success' : 'warning'),
        ];
    }
}
