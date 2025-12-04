<?php

namespace App\Filament\Resources\Correspondences\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CorrespondencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ref_number')->label('Ref No.')->searchable(),
                TextColumn::make('conference.title')->label('Conference'),
                TextColumn::make('category')->label('Category'),
                TextColumn::make('status')->label('Status'),
                TextColumn::make('correspondence_date')->date()->label('Date')->sortable(),
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


