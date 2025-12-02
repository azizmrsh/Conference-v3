<?php

namespace App\Filament\Resources\TravelBookings\Pages;

use App\Filament\Resources\TravelBookings\TravelBookingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class EditTravelBooking extends EditRecord
{
    protected static string $resource = TravelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $type = $data['type'] ?? null;

        if (in_array($type, ['arrival_flight','departure_flight'])) {
            foreach (['airline_id','flight_number','flight_date','flight_time','airport_from_id','airport_to_id'] as $field) {
                if (empty($data[$field])) {
                    throw ValidationException::withMessages([
                        $field => 'الحقول الخاصة بالرحلة مطلوبة لنوع الحجز المختار.',
                    ]);
                }
            }
        }

        if ($type === 'hotel') {
            foreach (['hotel_id','room_type','check_in','check_out'] as $field) {
                if (empty($data[$field])) {
                    throw ValidationException::withMessages([
                        $field => 'حقول الفندق مطلوبة عند اختيار نوع فندق.',
                    ]);
                }
            }

            if (!empty($data['check_in']) && !empty($data['check_out'])) {
                $in = Carbon::parse($data['check_in']);
                $out = Carbon::parse($data['check_out']);
                if ($out->lt($in)) {
                    throw ValidationException::withMessages([
                        'check_out' => 'تاريخ المغادرة يجب أن يكون بعد تاريخ الوصول.',
                    ]);
                }
            }
        }

        return $data;
    }
}

