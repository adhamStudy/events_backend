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
use Illuminate\Support\Facades\Log;

class MessagesResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Message Requests';

    protected static ?string $modelLabel = 'Message Request';

    protected static ?string $pluralModelLabel = 'Message Requests';

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

                // Add these debug fields to see the actual values
                Forms\Components\TextInput::make('provider_id')
                    ->label('Provider ID')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('user.name')
                            ->label('')
                            ->size('lg')
                            ->weight('bold')
                            ->color('primary')
                            ->prefix('From: '),

                        Tables\Columns\TextColumn::make('title')
                            ->label('')
                            ->size('md')
                            ->weight('semibold')
                            ->color('gray')
                            ->wrap(),

                        Tables\Columns\TextColumn::make('body')
                            ->label('')
                            ->wrap()
                            ->lineClamp(3)
                            ->html()
                            ->formatStateUsing(
                                fn(string $state): string =>
                                '<div class="text-gray-700 mt-1">' . nl2br(e($state)) . '</div>'
                            ),

                        Tables\Columns\TextColumn::make('created_at')
                            ->label('')
                            ->since()
                            ->color('gray')
                            ->size('sm')
                            ->prefix('Received: '),
                    ])->space(2),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\BadgeColumn::make('status')
                            ->colors([
                                'warning' => 'pending',
                                'success' => 'success',
                                'danger' => 'ignored',
                            ])
                            ->icons([
                                'pending' => 'heroicon-m-clock',
                                'success' => 'heroicon-m-check-circle',
                                'ignored' => 'heroicon-m-x-circle',
                            ])
                            ->size('lg'),
                    ])->alignment('end'),
                ])->from('md'),
            ])
            ->contentGrid([
                'md' => 1,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Accepted',
                        'ignored' => 'Rejected',
                    ])
                    ->default('pending'),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size('lg')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading('Accept Message')
                    ->modalDescription('Are you sure you want to accept this message?')
                    ->modalSubmitActionLabel('Yes, Accept')
                    ->action(function (Message $record) {
                        $record->update(['status' => 'success']);

                        \Filament\Notifications\Notification::make()
                            ->title('Message Accepted')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(Message $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->size('lg')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading('Reject Message')
                    ->modalDescription('Are you sure you want to reject this message?')
                    ->modalSubmitActionLabel('Yes, Reject')
                    ->action(function (Message $record) {
                        $record->update(['status' => 'ignored']);

                        \Filament\Notifications\Notification::make()
                            ->title('Message Rejected')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(Message $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('view_details')
                    ->label('View Full Message')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn(Message $record) => $record->title)
                    ->modalContent(fn(Message $record) => view('filament.modals.message-details', ['message' => $record]))
                    ->modalWidth('lg')
                    ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('accept_selected')
                        ->label('Accept Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Accept Selected Messages')
                        ->modalDescription('Are you sure you want to accept all selected messages?')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'success']);
                            });

                            \Filament\Notifications\Notification::make()
                                ->title('Messages Accepted')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Selected Messages')
                        ->modalDescription('Are you sure you want to reject all selected messages?')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'ignored']);
                            });

                            \Filament\Notifications\Notification::make()
                                ->title('Messages Rejected')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Auto-refresh every 30 seconds for new messages
            ->emptyStateHeading('No Messages')
            ->emptyStateDescription('You have no messages at the moment.')
            ->emptyStateIcon('heroicon-o-inbox');
    }

    public static function getEloquentQuery(): Builder
    {
        $currentUserId = Auth::id(); // This gives us user_id = 1

        // Get the provider_id that belongs to this user
        // Assuming you have a providers table or user relationship to get provider_id
        // Method 1: If you have a providers table
        $currentProviderId = \App\Models\Provider::where('user_id', $currentUserId)->value('id');

        // Method 2: If provider info is in users table
        // $currentProviderId = Auth::user()->provider_id;

        // Method 3: If you have a different relationship
        // $currentProviderId = Auth::user()->provider->id;

        Log::info('Current user ID: ' . $currentUserId);
        Log::info('Current provider ID: ' . $currentProviderId);

        return parent::getEloquentQuery()
            ->where('provider_id', $currentProviderId)
            ->with(['user']);
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
