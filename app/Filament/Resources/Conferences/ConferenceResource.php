<?php

namespace App\Filament\Resources\Conferences;

use App\Filament\Resources\Conferences\Pages\CreateConference;
use App\Filament\Resources\Conferences\Pages\EditConference;
use App\Filament\Resources\Conferences\Pages\ListConferences;
use App\Filament\Resources\Conferences\Pages\ViewConference;
use App\Filament\Resources\Conferences\RelationManagers\SessionsRelationManager;
use App\Filament\Resources\Conferences\RelationManagers\TopicsRelationManager;
use App\Filament\Resources\Conferences\Schemas\ConferenceForm;
use App\Filament\Resources\Conferences\Tables\ConferencesTable;
use App\Models\Conference;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationGroup = 'Pre-Conference';

    protected static ?int $navigationSort = 110;

    protected static ?string $slug = 'conferences';

    public static function form(Form $form): Form
    {
        return ConferenceForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ConferencesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TopicsRelationManager::class,
            SessionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConferences::route('/'),
            'create' => CreateConference::route('/create'),
            'edit' => EditConference::route('/{record}/edit'),
            'view' => ViewConference::route('/{record}'),
        ];
    }
}
