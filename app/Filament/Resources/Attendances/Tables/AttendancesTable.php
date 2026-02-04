<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('conference.title')->label('Conference'),
                TextColumn::make('session.session_title')->label('Session'),
                TextColumn::make('member.full_name')->label('Member'),
                TextColumn::make('check_in_at')->dateTime()->label('Check-in'),
                TextColumn::make('check_out_at')->dateTime()->label('Check-out'),
                TextColumn::make('method')->label('Method'),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
