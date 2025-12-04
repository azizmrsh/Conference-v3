<?php

namespace App\Filament\Resources\PressReleases\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PressReleasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Title')->searchable(),
                TextColumn::make('mediaCampaign.title')->label('Campaign'),
                TextColumn::make('release_type')->label('Type')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('scheduled_release_time')->dateTime()->label('Scheduled'),
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
