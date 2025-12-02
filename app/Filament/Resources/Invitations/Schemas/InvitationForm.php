<?php

namespace App\Filament\Resources\Invitations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Validation\Rule;

class InvitationForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Invitation Info')
                    ->icon('heroicon-o-envelope')
                    ->columns(2)
                    ->schema([
                        Select::make('conference_id')
                            ->label('Conference')
                            ->relationship('conference', 'title_ar')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('member_id')
                            ->label('Member')
                            ->relationship('member', 'first_name_ar')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Section::make('Status & Role')
                    ->icon('heroicon-o-flag')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'nominated' => 'مرشح',
                                'invited' => 'مدعو',
                                'confirmed_attendance' => 'أكد الحضور',
                                'apologized_attendance' => 'اعتذر عن الحضور',
                                'apologized_writing' => 'اعتذر عن الكتابة',
                                'no_response' => 'بدون رد',
                            ])
                            ->required()
                            ->rule(Rule::in(['nominated','invited','confirmed_attendance','apologized_attendance','apologized_writing','no_response'])),
                        Select::make('role')
                            ->label('Role')
                            ->options([
                                'researcher' => 'باحث',
                                'listener' => 'مستمع',
                                'session_chair' => 'رئيس جلسة',
                                'media' => 'إعلام',
                                'guest_honor' => 'ضيف شرف',
                            ])
                            ->required()
                            ->rule(Rule::in(['researcher','listener','session_chair','media','guest_honor'])),
                        DateTimePicker::make('invitation_sent_at')->label('Invitation Sent At'),
                        DateTimePicker::make('response_received_at')->label('Response Received At'),
                    ]),

                Section::make('Logistics Checklist')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->columns(3)
                    ->schema([
                        Toggle::make('needs_visa')->label('Needs Visa')->default(true),
                        Toggle::make('visa_issued')->label('Visa Issued'),
                        Toggle::make('flight_booked')->label('Flight Booked'),
                        Toggle::make('hotel_booked')->label('Hotel Booked'),
                        Toggle::make('bag_received')->label('Bag Received'),
                        Toggle::make('badge_printed')->label('Badge Printed'),
                    ]),

                Section::make('Requirements')
                    ->icon('heroicon-o-exclamation-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('emergency_contact')->label('Emergency Contact'),
                        Textarea::make('dietary_requirements')->label('Dietary Requirements'),
                        Textarea::make('accessibility_needs')->label('Accessibility Needs'),
                        Textarea::make('documents_received')->label('Documents Received'),
                    ]),

                Section::make('Travel Dates')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('expected_arrival')->label('Expected Arrival'),
                        DateTimePicker::make('expected_departure')->label('Expected Departure'),
                    ]),

                Section::make('Communication')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->columns(2)
                    ->schema([
                        Select::make('preferred_communication')
                            ->label('Preferred Communication')
                            ->options([
                                'email' => 'بريد إلكتروني',
                                'fax' => 'فاكس',
                                'phone' => 'هاتف',
                                'post' => 'بريد عادي',
                            ])
                            ->default('email'),
                        TextInput::make('reminders_sent')->label('Reminders Sent')->numeric()->minValue(0),
                        Textarea::make('notes')->label('Notes')->columnSpanFull(),
                    ]),
            ]);
    }
}

