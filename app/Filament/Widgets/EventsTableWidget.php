<?php

namespace App\Filament\Widgets;

use App\Models\City;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class EventsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Cities & Events Overview';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return City::query()->withCount('events')->orderBy('events_count', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('City Name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('events_count')
                ->label('Total Events')
                ->badge()
                ->color(fn(string $state): string => match (true) {
                    $state >= 10 => 'success',
                    $state >= 5 => 'warning',
                    $state >= 1 => 'info',
                    default => 'gray',
                })
                ->sortable(),

            Tables\Columns\TextColumn::make('latitude')
                ->label('Latitude')
                ->numeric(2)
                ->toggleable(),

            Tables\Columns\TextColumn::make('longitude')
                ->label('Longitude')
                ->numeric(2)
                ->toggleable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Added')
                ->dateTime()
                ->since()
                ->toggleable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('has_events')
                ->label('Cities with Events')
                ->query(fn(Builder $query): Builder => $query->has('events')),

            Tables\Filters\Filter::make('no_events')
                ->label('Cities without Events')
                ->query(fn(Builder $query): Builder => $query->doesntHave('events')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view_events')
                ->label('View Events')
                ->icon('heroicon-m-eye')
                ->url(fn(City $record): string => "/admin/events?tableFilters[city][value]={$record->id}")
                ->visible(fn(City $record): bool => $record->events_count > 0),
        ];
    }
}
