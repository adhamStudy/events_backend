<?php

namespace App\Filament\Provider\Resources;

use Afsakar\LeafletMapPicker\LeafletMapPicker;

use App\Filament\Provider\Resources\EventResource\Pages;
use App\Filament\Provider\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Event Name')
                ->required()
                ->maxLength(255),

            TextInput::make('capacity')
                ->label('Number of Chears')
                ->required()
                ->integer(),

            DateTimePicker::make('start_time')
                ->label('Start Time')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                    $endTime = $get('end_time');
                    if ($endTime && $state && $endTime <= $state) {
                        $set('end_time', null);
                    }
                }),

            DateTimePicker::make('end_time')
                ->label('End Time')
                ->required()
                ->live()
                ->minDate(function (Forms\Get $get) {
                    $startTime = $get('start_time');
                    return $startTime ? \Carbon\Carbon::parse($startTime)->addMinute() : now()->startOfDay();
                })
                ->rules([
                    function (Forms\Get $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $startTime = $get('start_time');

                            if (!$startTime || !$value) {
                                return;
                            }

                            $start = \Carbon\Carbon::parse($startTime);
                            $end = \Carbon\Carbon::parse($value);

                            if ($end <= $start) {
                                $fail('End time must be after start time.');
                            }
                        };
                    },
                ]),

            Textarea::make('description')
                ->label('Description')
                ->maxLength(500),

            Select::make('city_id')
                ->label('City')
                ->relationship('city', 'name')
                ->required(),

            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->required(),

            FileUpload::make('image')
                ->directory('events')
                ->image()
                ->panelLayout('integrated')
                ->preserveFilenames()
                ->maxSize(5000)
                ->disk('public')
                ->nullable(),

            LeafletMapPicker::make('location')
                ->label('Pick Event Location')
                ->defaultZoom(15)
                ->defaultLocation(function ($get) {
                    return $get('latitude') && $get('longitude')
                        ? [(float)$get('latitude'), (float)$get('longitude')]
                        : [23.8859, 45.0792];
                })
                ->clickable()
                ->draggable()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    $set('latitude', $state['lat']);
                    $set('longitude', $state['lng']);
                }),

            Hidden::make('latitude')->required(),
            Hidden::make('longitude')->required(),

            // ✅ الحقل الخاص بـ provider_id
            Hidden::make('provider_id')
                ->default(fn() => Filament::auth()->user()->provider->id)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('city.name')->label('City')->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable(),
                Tables\Columns\TextColumn::make('start_time')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('end_time')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('capacity'),
                Tables\Columns\TextColumn::make('available_seats'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('provider_id', Filament::auth()->user()->provider->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
