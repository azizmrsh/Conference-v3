<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_name')->label('Item')->searchable(),
                TextColumn::make('tx_type')->label('Type')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('actual_amount')->label('Amount')->sortable(),
                TextColumn::make('incurred_at')->date()->label('Date')->sortable(),
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
