<?php

namespace App\Filament\Resources\Conferences\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ConferencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('sessions_count')
                    ->label('Sessions')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('topics_count')
                    ->label('Topics')
                    ->counts('topics')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planning' => 'gray',
                        'active' => 'warning',
                        'completed' => 'success',
                        'archived' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Filter::make('upcoming')
                    ->label('Upcoming Conferences')
                    ->query(fn ($query) => $query->where('start_date', '>=', now())),

                Filter::make('past')
                    ->label('Past Conferences')
                    ->query(fn ($query) => $query->where('end_date', '<', now())),

                Filter::make('active')
                    ->label('Active Conferences')
                    ->query(fn ($query) => $query->where('status', 'active')),

                Filter::make('planning')
                    ->label('In Planning')
                    ->query(fn ($query) => $query->where('status', 'planning')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
