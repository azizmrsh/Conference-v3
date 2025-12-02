<?php

namespace App\Filament\Resources\Conferences\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ConferenceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->icon('heroicon-o-information-circle')
                    ->description('Enter the main conference details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Conference Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Annual Medical Conference 2025')
                            ->columnSpanFull(),

                        TextInput::make('location')
                            ->label('Location')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Riyadh, Saudi Arabia')
                            ->columnSpanFull(),

                        TextInput::make('venue_name')
                            ->label('Venue Name')
                            ->maxLength(255)
                            ->placeholder('e.g., King Fahd Conference Hall')
                            ->columnSpan(1),

                        TextInput::make('venue_address')
                            ->label('Venue Address')
                            ->maxLength(500)
                            ->columnSpan(1),

                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->afterOrEqual('start_date')
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'planning' => 'Planning',
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'archived' => 'Archived',
                            ])
                            ->default('planning')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('sessions_count')
                            ->label('Sessions Count')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->helperText('Auto-calculated from sessions')
                            ->columnSpan(1),

                        TextInput::make('session_number')
                            ->label('Session Number')
                            ->numeric()
                            ->columnSpan(1),
                    ]),

                Section::make('Additional Details')
                    ->icon('heroicon-o-document-text')
                    ->description('Optional conference information')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        DatePicker::make('hijri_date')
                            ->label('Hijri Date')
                            ->native(false)
                            ->columnSpan(1),

                        DatePicker::make('gregorian_date')
                            ->label('Gregorian Date')
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

