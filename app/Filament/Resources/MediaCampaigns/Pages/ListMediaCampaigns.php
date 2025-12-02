<?php

namespace App\Filament\Resources\MediaCampaigns\Pages;

use App\Filament\Resources\MediaCampaigns\MediaCampaignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMediaCampaigns extends ListRecords
{
    protected static string $resource = MediaCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

