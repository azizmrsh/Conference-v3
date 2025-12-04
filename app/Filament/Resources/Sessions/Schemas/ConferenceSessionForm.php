<?php

namespace App\Filament\Resources\Sessions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ConferenceSessionForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Session Info')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->columns(2)
                    ->schema([
                        Select::make('conference_id')
                            ->label('Conference')
                            ->relationship('conference', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('session_title')->label('Session Title')->required(),
                        Select::make('topic_id')
                            ->label('Topic')
                            ->relationship('topic', 'title')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Schedule & Venue')
                    ->icon('heroicon-o-clock')
                    ->columns(3)
                    ->schema([
                        DateTimePicker::make('date')->label('Date')->required(),
                        DateTimePicker::make('start_time')->label('Start Time')->required(),
                        DateTimePicker::make('end_time')->label('End Time')->required(),
                        TextInput::make('hall_name')->label('Hall'),
                    ]),

                Section::make('Management')
                    ->icon('heroicon-o-users')
                    ->columns(2)
                    ->schema([
                        Select::make('chair_member_id')
                            ->label('Session Chair')
                            ->relationship('chair', 'full_name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('session_order')->numeric()->minValue(0)->default(0)->label('Order'),
                    ]),
            ]);
    }
}

