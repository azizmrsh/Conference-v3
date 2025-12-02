<?php

namespace App\Filament\Resources\Hotels\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

class HotelForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Hotel')
                    ->icon('heroicon-o-building-office')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Name')->required(),
                        TextInput::make('address')->label('Address'),
                        TextInput::make('contact_person')->label('Contact Person'),
                        TextInput::make('phone')->label('Phone'),
                        TextInput::make('email')->label('Email')->email(),
                        TextInput::make('rating')->numeric()->minValue(0)->maxValue(5)->label('Rating'),
                        Textarea::make('notes')->label('Notes')->columnSpanFull(),
                        Select::make('created_by')
                            ->label('Created By')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}


