<?php

namespace App\Filament\Resources;
use Afsakar\LeafletMapPicker\LeafletMapPicker;
use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
    ->schema([
        TextInput::make('name')
            ->label('Event Name')
            ->required()
            ->maxLength(255),

        Forms\Components\DateTimePicker::make('start_time')
            ->label('Start Time')
            ->required(),

        Forms\Components\DateTimePicker::make('end_time')
            ->label('End Time')
            ->required(),

        Forms\Components\Textarea::make('description')
            ->label('Description')
            ->maxLength(500),
            
        Forms\Components\Select::make('city_id')
            ->label('City')
            ->relationship('city', 'name')
            ->required(),
            
        Forms\Components\Select::make('category_id')
            ->label('Category')
            ->relationship('category', 'name')
            ->required(),
            
        // Forms\Components\FileUpload::make('image')
        //     ->label('Event Image')
        //     ->image()
        //     ->maxSize(1024),
            
        // Hidden latitude/longitude fields
        LeafletMapPicker::make('location')
    ->label('Pick Event Location')
    ->defaultZoom(6)
    ->defaultLocation(function ($get) {
        // Use saved coordinates if they exist, otherwise default to Saudi Arabia
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

Forms\Components\Hidden::make('latitude')
    ->required(),
    

Forms\Components\Hidden::make('longitude')
    ->required()
    ,
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
