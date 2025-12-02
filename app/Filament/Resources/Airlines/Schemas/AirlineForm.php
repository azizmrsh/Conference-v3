<?php

namespace App\Filament\Resources\Airlines\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class AirlineForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Airline')
                    ->icon('heroicon-o-paper-airplane')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Name')->required(),
                        TextInput::make('iata_code')->label('IATA Code')->maxLength(3),
                        TextInput::make('contact_phone')->label('Contact Phone'),
                        TextInput::make('contact_email')->label('Contact Email')->email(),
                        Select::make('created_by')
                            ->label('Created By')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}


