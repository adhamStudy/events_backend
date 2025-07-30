<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'canceled' => 'Canceled',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $old, Forms\Get $get) {
                        $eventId = $get('event_id');
                        if (!$eventId || !$old) return;

                        $event = \App\Models\Event::find($eventId);
                        if (!$event) return;

                        // If changing from success/pending to canceled - increase seats
                        if (in_array($old, ['success', 'pending']) && $state === 'canceled') {
                            $event->increment('available_seats');
                        }

                        // If changing from canceled to success/pending - decrease seats
                        if ($old === 'canceled' && in_array($state, ['success', 'pending'])) {
                            if ($event->available_seats > 0) {
                                $event->decrement('available_seats');
                            }
                        }
                    }),

                Forms\Components\DateTimePicker::make('booking_date')
                    ->label('Booking Date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(Booking $record): string =>
                        "Available seats: " . ($record->event->available_seats ?? 0)
                    ),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'success',
                        'danger' => 'canceled',
                    ]),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Booking Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'canceled' => 'Canceled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data, Booking $record): array {
                        // Store old status for comparison
                        $data['_old_status'] = $record->status;
                        return $data;
                    })
                    ->using(function (Booking $record, array $data): Booking {
                        $oldStatus = $data['_old_status'] ?? $record->status;
                        $newStatus = $data['status'];

                        // Update available seats based on status change
                        if ($oldStatus !== $newStatus && $record->event) {
                            // If changing from success/pending to canceled - increase seats
                            if (in_array($oldStatus, ['success', 'pending']) && $newStatus === 'canceled') {
                                $record->event->increment('available_seats');
                            }

                            // If changing from canceled to success/pending - decrease seats
                            if ($oldStatus === 'canceled' && in_array($newStatus, ['success', 'pending'])) {
                                if ($record->event->available_seats > 0) {
                                    $record->event->decrement('available_seats');
                                }
                            }
                        }

                        // Remove the helper field before saving
                        unset($data['_old_status']);

                        $record->update($data);
                        return $record;
                    }),

                Tables\Actions\DeleteAction::make()
                    ->before(function (Booking $record) {
                        // Increase available seats when deleting a success/pending booking
                        if ($record->event && in_array($record->status, ['success', 'pending'])) {
                            $record->event->increment('available_seats');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Increase available seats for each deleted success/pending booking
                            foreach ($records as $record) {
                                if ($record->event && in_array($record->status, ['success', 'pending'])) {
                                    $record->event->increment('available_seats');
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
