<?php

namespace App\Filament\Resources\TravelBookings\Pages;

use App\Filament\Resources\TravelBookings\TravelBookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTravelBookings extends ListRecords
{
    protected static string $resource = TravelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

