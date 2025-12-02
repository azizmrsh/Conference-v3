<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

class CountryForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Names')
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Name')->required(),
                    ]),
                Section::make('Details')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('iso_code')->label('ISO Code')->maxLength(3),
                        Select::make('created_by')
                            ->label('Created By')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload(),
                        Toggle::make('visa_required_for_jordan')->label('Visa Required for Jordan')->default(true)->columnSpanFull(),
                    ]),
            ]);
    }
}


