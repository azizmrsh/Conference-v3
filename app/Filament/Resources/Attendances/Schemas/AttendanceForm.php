<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Validation\Rule;

class AttendanceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Event Details')
                    ->icon('heroicon-o-calendar')
                    ->columns(2)
                    ->schema([
                        Select::make('conference_id')
                            ->label('Conference')
                            ->relationship('conference', 'title_ar')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('conference_session_id')
                            ->label('Session')
                            ->relationship('session', 'title_ar')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Attendee Info')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Select::make('member_id')
                            ->label('Member')
                            ->relationship('member', 'full_name')
                            ->searchable()
                            ->preload(),
                        Select::make('invitation_id')
                            ->label('Invitation')
                            ->relationship('invitation', 'id')
                            ->searchable()
                            ->preload(),
                        TextInput::make('badge_code')->label('Badge Code')->columnSpanFull(),
                    ]),

                Section::make('Check-in Details')
                    ->icon('heroicon-o-clock')
                    ->columns(3)
                    ->schema([
                        Select::make('method')
                            ->label('Registration Method')
                            ->options([
                                'scan' => 'Scan',
                                'manual' => 'Manual',
                            ])
                            ->default('scan')
                            ->rule(Rule::in(['scan', 'manual'])),
                        DateTimePicker::make('check_in_at')->label('Check-in Time'),
                        DateTimePicker::make('check_out_at')->label('Check-out Time'),
                    ]),

                Section::make('Notes')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        Textarea::make('notes')->label('Notes')->columnSpanFull(),
                    ]),
            ]);
    }
}
