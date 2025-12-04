<?php

namespace App\Filament\Resources\CommitteeMembers\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class CommitteeMemberForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Assignment Details')
                    ->icon('heroicon-o-briefcase')
                    ->columns(2)
                    ->schema([
                        Select::make('committee_id')
                            ->label('Committee')
                            ->relationship('committee', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('member_id')
                            ->label('Member')
                            ->relationship('member', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('role')
                            ->label('Role')
                            ->default('member')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
