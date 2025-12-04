<?php

namespace App\Filament\Resources\Sessions\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConferenceSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('conference.title')
                    ->label('Conference')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('session_title')
                    ->label('Session Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('topic.title')
                    ->label('Topic')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time('H:i'),
                TextColumn::make('end_time')
                    ->label('End Time')
                    ->time('H:i'),
                TextColumn::make('hall_name')
                    ->label('Hall')
                    ->searchable(),
                TextColumn::make('chair.full_name')
                    ->label('Chair')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('session_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('session_order');
    }
}

