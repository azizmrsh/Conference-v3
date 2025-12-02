<?php

namespace App\Filament\Resources\CommitteeMembers;

use App\Filament\Resources\CommitteeMembers\Pages\CreateCommitteeMember;
use App\Filament\Resources\CommitteeMembers\Pages\EditCommitteeMember;
use App\Filament\Resources\CommitteeMembers\Pages\ListCommitteeMembers;
use App\Filament\Resources\CommitteeMembers\Schemas\CommitteeMemberForm;
use App\Filament\Resources\CommitteeMembers\Tables\CommitteeMembersTable;
use App\Models\CommitteeMember;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
class CommitteeMemberResource extends Resource
{
    protected static ?string $model = CommitteeMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Conference Operations';

    protected static ?int $navigationSort = 710;

    public static function form(Form $form): Form
    {
        return CommitteeMemberForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return CommitteeMembersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommitteeMembers::route('/'),
            'create' => CreateCommitteeMember::route('/create'),
            'edit' => EditCommitteeMember::route('/{record}/edit'),
        ];
    }
}
