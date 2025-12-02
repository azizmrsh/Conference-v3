<?php

namespace App\Filament\Resources\Sessions\Pages;

use App\Filament\Resources\Sessions\ConferenceSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConferenceSessions extends ListRecords
{
    protected static string $resource = ConferenceSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

