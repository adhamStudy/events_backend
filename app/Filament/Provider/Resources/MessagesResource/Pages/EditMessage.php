<?php

namespace App\Filament\Provider\Resources\MessagesResource\Pages;

use App\Filament\Provider\Resources\MessagesResource;
use Filament\Resources\Pages\EditRecord;

class EditMessage extends EditRecord
{
    protected static string $resource = MessagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No delete action to prevent accidental deletion
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
