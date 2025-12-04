<?php

namespace App\Filament\Resources\Correspondences\Pages;

use App\Filament\Resources\Correspondences\CorrespondenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCorrespondence extends EditRecord
{
    protected static string $resource = CorrespondenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-set updated_by
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
