<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('email')->email()->required(),
            Select::make('role')
                ->required()
                ->options([
                    'admin' => 'Admin',
                    'provider' => 'Provider',
                    'visitor' => 'Visitor',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('email')->searchable(),
            BadgeColumn::make('role')
                ->colors([
                    'primary' => 'admin',
                    'success' => 'provider',
                    'gray' => 'visitor',
                ]),
        ])
            ->filters([
                Tables\Filters\Filter::make('email')
                    ->form([
                        TextInput::make('email')->label('Email contains'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['email'],
                            fn($q, $email) =>
                            $q->where('email', 'like', "%{$email}%")
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
