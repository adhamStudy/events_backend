<?php

namespace App\Filament\Provider\Resources\MessagesResource\Pages;

use App\Filament\Provider\Resources\MessagesResource;
use Filament\Resources\Pages\ListRecords;

class ListMessages extends ListRecords
{
    protected static string $resource = MessagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - users can't create messages from provider panel
        ];
    }
}
