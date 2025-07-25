<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PopularCategoryWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $topCategory = Category::withCount('events')
            ->orderBy('events_count', 'desc')
            ->first();

        $totalCategories = Category::count();
        $categoriesWithEvents = Category::has('events')->count();
        $totalEvents = Event::count();

        return [
            Stat::make('Most Popular Category', $topCategory ? $topCategory->name : 'No events yet')
                ->description($topCategory ? "{$topCategory->events_count} events" : 'Create some events first')
                ->descriptionIcon($topCategory ? 'heroicon-m-star' : 'heroicon-m-exclamation-triangle')
                ->color($topCategory ? 'success' : 'warning'),

            Stat::make('Active Categories', $categoriesWithEvents)
                ->description("Out of {$totalCategories} total categories")
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make('Total Events', $totalEvents)
                ->description('Across all categories')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
