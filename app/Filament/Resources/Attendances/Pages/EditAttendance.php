<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['check_in_at']) && !empty($data['check_out_at'])) {
            $in = Carbon::parse($data['check_in_at']);
            $out = Carbon::parse($data['check_out_at']);
            if ($out->lt($in)) {
                throw ValidationException::withMessages([
                    'check_out_at' => 'وقت الخروج يجب أن يكون بعد وقت الدخول.',
                ]);
            }
        }

        return $data;
    }
}

