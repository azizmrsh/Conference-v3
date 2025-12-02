<?php

namespace App\Filament\Resources\Sessions\Pages;

use App\Filament\Resources\Sessions\ConferenceSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConferenceSession extends EditRecord
{
    protected static string $resource = ConferenceSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

