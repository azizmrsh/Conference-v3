<?php

namespace App\Filament\Resources\BadgesKits\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class BadgesKitForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Badge/Kit')
                    ->icon('heroicon-o-identification')
                    ->columns(3)
                    ->schema([
                        Select::make('conference_id')->label('Conference')->relationship('conference', 'title')->searchable()->preload()->required(),
                        Select::make('item_type')->label('Item Type')->options([
                            'staff_badge' => 'Staff Badge',
                            'member_badge' => 'Member Badge',
                            'guest_badge' => 'Guest Badge',
                            'press_badge' => 'Press Badge',
                            'bag' => 'Bag',
                            'dvd' => 'DVD',
                        ])->required(),
                        Textarea::make('description')->label('Description')->columnSpanFull(),
                        TextInput::make('quantity')->label('Quantity')->numeric()->minValue(0)->required(),
                        TextInput::make('cost_per_item')->label('Cost/Item')->numeric()->minValue(0),
                    ]),
            ]);
    }
}
