<?php

namespace App\Filament\Resources\BadgesKits\Pages;

use App\Filament\Resources\BadgesKits\BadgesKitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBadgesKits extends ListRecords
{
    protected static string $resource = BadgesKitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

