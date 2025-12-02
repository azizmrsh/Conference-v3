<?php

namespace App\Filament\Resources\BadgesKits\Pages;

use App\Filament\Resources\BadgesKits\BadgesKitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBadgesKit extends EditRecord
{
    protected static string $resource = BadgesKitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

