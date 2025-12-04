<?php

namespace App\Filament\Resources\Correspondences\Pages;

use App\Filament\Resources\Correspondences\CorrespondenceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCorrespondence extends CreateRecord
{
    protected static string $resource = CorrespondenceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set created_by
        $data['created_by'] = auth()->id();

        // Auto-set correspondence_date if not provided
        if (empty($data['correspondence_date'])) {
            $data['correspondence_date'] = now();
        }

        // Auto-generate ref_number if not provided
        if (empty($data['ref_number']) && ! empty($data['category'])) {
            $correspondence = new \App\Models\Correspondence;
            $correspondence->category = $data['category'];
            $data['ref_number'] = $correspondence->generateRefNumber();
        }

        return $data;
    }
}
