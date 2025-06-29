<?php

namespace App\Filament\Provider\Resources\EventResource\Pages;

use App\Filament\Provider\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;
}
