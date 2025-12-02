<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Validation\Rule;

class MemberForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')->label('First Name')->required(),
                        TextInput::make('last_name')->label('Last Name')->required(),
                        TextInput::make('honorific_title')->label('Honorific Title'),
                        TextInput::make('academic_title')->label('Academic Title'),
                        TextInput::make('photo_path')->label('Photo Path')->columnSpanFull(),
                    ]),

                Section::make('Membership Details')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'working_member' => 'عضو عامل',
                                'correspondent_member' => 'عضو مراسل',
                                'honorary_member' => 'عضو شرف',
                                'guest_speaker' => 'ضيف متحدث',
                                'staff' => 'موظف',
                                'journalist' => 'صحفي',
                            ])
                            ->required()
                            ->searchable()
                            ->rule(Rule::in(['working_member','correspondent_member','honorary_member','guest_speaker','staff','journalist'])),
                        DatePicker::make('membership_date')->label('Membership Date'),
                        Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                    ]),

                Section::make('Identification')
                    ->icon('heroicon-o-document-text')
                    ->columns(3)
                    ->schema([
                        Select::make('nationality_id')
                            ->label('Nationality')
                            ->relationship('nationality', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('passport_number')->label('Passport Number'),
                        DatePicker::make('passport_expiry')->label('Passport Expiry'),
                    ]),

                Section::make('Contact & Bio')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')->email()->prefixIcon('heroicon-m-envelope'),
                        TextInput::make('phone')->tel()->prefixIcon('heroicon-m-phone'),
                        Textarea::make('cv_text')->label('CV')->columnSpanFull(),
                    ]),
            ]);
    }
}

