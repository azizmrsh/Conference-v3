<?php

namespace App\Filament\Resources\Committees\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class CommitteeForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Committee Details')
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->schema([
                        Select::make('conference_id')->label('Conference')->relationship('conference','title')->searchable()->preload()->required(),
                        Select::make('created_by')->label('Created By')->relationship('creator','name')->searchable()->preload(),
                        TextInput::make('name')->label('Name')->required(),
                    ]),

                Section::make('Description')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Textarea::make('description')->label('Description')->columnSpanFull(),
                    ]),
            ]);
    }
}


