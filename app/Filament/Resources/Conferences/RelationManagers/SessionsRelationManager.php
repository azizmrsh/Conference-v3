<?php

namespace App\Filament\Resources\Conferences\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Form;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    protected static ?string $recordTitleAttribute = 'session_title';

    protected static ?string $title = 'Sessions';

    protected static ?string $icon = 'heroicon-o-presentation-chart-line';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('session_title')
                    ->label('Session Title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('topic_id')
                    ->label('Topic')
                    ->relationship('topic', 'title')
                    ->searchable()
                    ->preload()
                    ->columnSpan(1),

                DateTimePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->columnSpan(1),

                DateTimePicker::make('start_time')
                    ->label('Start Time')
                    ->required()
                    ->columnSpan(1),

                DateTimePicker::make('end_time')
                    ->label('End Time')
                    ->required()
                    ->columnSpan(1),

                TextInput::make('hall_name')
                    ->label('Hall Name')
                    ->maxLength(255)
                    ->columnSpan(1),

                Select::make('chair_member_id')
                    ->label('Session Chair')
                    ->relationship('chair', 'full_name')
                    ->searchable()
                    ->preload()
                    ->columnSpan(1),

                TextInput::make('session_order')
                    ->label('Order')
                    ->numeric()
                    ->default(0)
                    ->columnSpan(1),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session_title')
                    ->label('Session')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('topic.title')
                    ->label('Topic')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Start')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label('End')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('hall_name')
                    ->label('Hall')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('chair.full_name')
                    ->label('Chair')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('session_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('session_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
