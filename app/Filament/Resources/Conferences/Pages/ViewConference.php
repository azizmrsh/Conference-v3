<?php

namespace App\Filament\Resources\Conferences\Pages;

use App\Filament\Resources\Conferences\ConferenceResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewConference extends ViewRecord
{
    protected static string $resource = ConferenceResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
