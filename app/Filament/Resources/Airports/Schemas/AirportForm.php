<?php

namespace App\Filament\Resources\Airports\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class AirportForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Airport')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Name')->required(),
                        TextInput::make('iata_code')->label('IATA Code')->maxLength(3)->required(),
                        TextInput::make('city')->label('City')->required(),
                        Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('created_by')
                            ->label('Created By')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}


