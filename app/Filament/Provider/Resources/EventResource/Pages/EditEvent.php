<?php

namespace App\Filament\Provider\Resources\EventResource\Pages;

use App\Filament\Provider\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
