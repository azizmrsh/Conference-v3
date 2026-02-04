<?php

namespace App\Filament\Resources\Invitations\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('conference.title')->label('Conference')->searchable(),
                TextColumn::make('member.full_name')->label('Member')->searchable(),
                TextColumn::make('role')->label('Role')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                IconColumn::make('needs_visa')->boolean()->label('Visa'),
                IconColumn::make('flight_booked')->boolean()->label('Flight'),
                IconColumn::make('hotel_booked')->boolean()->label('Hotel'),
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
