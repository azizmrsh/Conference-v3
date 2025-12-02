<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Account Details')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->prefixIcon('heroicon-m-envelope'),
                        Select::make('roles')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->label('Roles')
                            ->columnSpanFull(),
                    ]),

                Section::make('Security')
                    ->icon('heroicon-o-lock-closed')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->confirmed()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        TextInput::make('password_confirmation')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->password()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}

