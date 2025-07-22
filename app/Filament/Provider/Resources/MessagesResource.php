<?php

namespace App\Filament\Provider\Resources;

use App\Filament\Provider\Resources\MessagesResource\Pages;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MessagesResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $modelLabel = 'Message';

    protected static ?string $pluralModelLabel = 'Messages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->disabled(), // Make it read-only

                Forms\Components\Textarea::make('body')
                    ->required()
                    ->disabled(), // Make it read-only

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'ignored' => 'Ignored',
                    ])
                    ->required()
                    ->default('pending'),

                Forms\Components\TextInput::make('user.name')
                    ->label('User Name')
                    ->disabled(),

                Forms\Components\TextInput::make('user.email')
                    ->label('User Email')
                    ->disabled(),
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
                    ->label('User Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('User Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('body')
                    ->label('Message')
                    ->limit(100)
                    ->tooltip(function (Message $record): string {
                        return $record->body;
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'success',
                        'danger' => 'ignored',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'ignored' => 'Ignored',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Update Status'),

                Tables\Actions\Action::make('mark_success')
                    ->label('Mark as Success')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Message $record) {
                        $record->update(['status' => 'success']);
                    })
                    ->visible(fn(Message $record) => $record->status !== 'success'),

                Tables\Actions\Action::make('mark_ignored')
                    ->label('Mark as Ignored')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (Message $record) {
                        $record->update(['status' => 'ignored']);
                    })
                    ->visible(fn(Message $record) => $record->status !== 'ignored'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_success')
                        ->label('Mark as Success')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'success']);
                            });
                        }),

                    Tables\Actions\BulkAction::make('mark_ignored')
                        ->label('Mark as Ignored')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'ignored']);
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        // Only show messages for the current provider (authenticated user)
        return parent::getEloquentQuery()
            ->where('provider_id', Auth::id())
            ->with(['user']); // Eager load user relationship
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
            'index' => Pages\ListMessages::route('/'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }

    // Hide create button by removing create page
    public static function canCreate(): bool
    {
        return false;
    }
}
