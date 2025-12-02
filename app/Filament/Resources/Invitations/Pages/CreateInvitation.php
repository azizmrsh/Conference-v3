<?php

namespace App\Filament\Resources\Invitations\Pages;

use App\Filament\Resources\Invitations\InvitationResource;
use App\Models\Member;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class CreateInvitation extends CreateRecord
{
    protected static string $resource = InvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['status'] ?? null) === 'confirmed_attendance' && empty($data['response_received_at'])) {
            throw ValidationException::withMessages([
                'response_received_at' => 'لا يمكن تأكيد الحضور بدون تسجيل وقت استلام الرد.',
            ]);
        }

        if (!empty($data['expected_arrival']) && !empty($data['expected_departure'])) {
            $arr = Carbon::parse($data['expected_arrival']);
            $dep = Carbon::parse($data['expected_departure']);
            if ($dep->lt($arr)) {
                throw ValidationException::withMessages([
                    'expected_departure' => 'المغادرة المتوقعة يجب أن تكون بعد أو مساوية للوصول المتوقع.',
                ]);
            }
        }

        if (!empty($data['member_id'])) {
            $member = Member::with('nationality')->find($data['member_id']);
            if ($member && $member->nationality && !$member->nationality->visa_required_for_jordan) {
                $data['needs_visa'] = false;
            }
        }

        return $data;
    }
}

