<?php

namespace App\Filament\Resources\Committees;

use App\Filament\Resources\Committees\Pages\CreateCommittee;
use App\Filament\Resources\Committees\Pages\EditCommittee;
use App\Filament\Resources\Committees\Pages\ListCommittees;
use App\Filament\Resources\Committees\Schemas\CommitteeForm;
use App\Filament\Resources\Committees\Tables\CommitteesTable;
use App\Models\Committee;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class CommitteeResource extends Resource
{
    protected static ?string $model = Committee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Scientific Committee';

    protected static ?int $navigationSort = 230;

    public static function form(Form $form): Form
    {
        return CommitteeForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return CommitteesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommittees::route('/'),
            'create' => CreateCommittee::route('/create'),
            'edit' => EditCommittee::route('/{record}/edit'),
        ];
    }
}
