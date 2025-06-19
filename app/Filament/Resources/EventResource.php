<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
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
                Forms\Components\FileUpload::make('image')
                    ->label('Event Image')
                    ->image()
                    ->maxSize(1024),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->required()
                    ->rule('between:-90,90'),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->required()
                    ->rule('between:-180,180'),  
                 // 1MB
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
