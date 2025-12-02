<?php

namespace App\Filament\Resources\Sessions;

use App\Filament\Resources\Sessions\Pages\CreateConferenceSession;
use App\Filament\Resources\Sessions\Pages\EditConferenceSession;
use App\Filament\Resources\Sessions\Pages\ListConferenceSessions;
use App\Filament\Resources\Sessions\Schemas\ConferenceSessionForm;
use App\Filament\Resources\Sessions\Tables\ConferenceSessionsTable;
use App\Models\ConferenceSession;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ConferenceSessionResource extends Resource
{
    protected static ?string $model = ConferenceSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $recordTitleAttribute = 'session_title';

    protected static ?string $navigationGroup = 'Conference Management';

    protected static ?int $navigationSort = 180;

    public static function form(Form $form): Form
    {
        return ConferenceSessionForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ConferenceSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConferenceSessions::route('/'),
            'create' => CreateConferenceSession::route('/create'),
            'edit' => EditConferenceSession::route('/{record}/edit'),
        ];
    }
}
