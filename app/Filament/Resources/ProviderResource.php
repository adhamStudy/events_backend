<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers;
use App\Models\Provider;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('User')
                ->options(
                    User::where('role', 'provider')->pluck('email', 'id')
                )
                ->searchable()
                ->required(),

            TextInput::make('name')->required(),
            TextInput::make('company_name')->nullable(),
            Textarea::make('description')->nullable(),

            FileUpload::make('profile_image')
                ->directory('providers') // folder inside storage/app/public
                ->image()             // restricts to image files
                ->panelAspectRatio('1:1')
                ->panelLayout('integrated')
                ->preserveFilenames()
                ->maxSize(5000)       // in KB
                ->disk('public')      // use 'public' disk
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('profile_image')->circular()->size(40),
            TextColumn::make('user.email')->label('User Email')->searchable(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('company_name')->searchable(),
        ])
            ->filters([
                Tables\Filters\Filter::make('email')
                    ->form([
                        TextInput::make('email'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['email'],
                            fn($q, $email) =>
                            $q->whereHas(
                                'user',
                                fn($q) =>
                                $q->where('email', 'like', "%{$email}%")
                            )
                        );
                    }),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
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
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
