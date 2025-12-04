<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paper.title_ar')->label('Paper'),
                TextColumn::make('reviewer.name')->label('Reviewer'),
                TextColumn::make('review_type')->label('Type')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('due_date')->date()->label('Due Date')->sortable(),
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
