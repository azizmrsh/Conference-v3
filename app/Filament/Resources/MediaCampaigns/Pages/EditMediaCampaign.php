<?php

namespace App\Filament\Resources\MediaCampaigns\Pages;

use App\Filament\Resources\MediaCampaigns\MediaCampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMediaCampaign extends EditRecord
{
    protected static string $resource = MediaCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

