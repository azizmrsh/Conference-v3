<?php

namespace App\Filament\Resources\Correspondences;

use App\Filament\Resources\Correspondences\Pages\CreateCorrespondence;
use App\Filament\Resources\Correspondences\Pages\EditCorrespondence;
use App\Filament\Resources\Correspondences\Pages\ListCorrespondences;
use App\Filament\Resources\Correspondences\Pages\ViewCorrespondence;
use App\Filament\Resources\Correspondences\Schemas\CorrespondenceForm;
use App\Filament\Resources\Correspondences\Tables\CorrespondencesTable;
use App\Models\Correspondence;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class CorrespondenceResource extends Resource
{
    protected static ?string $model = Correspondence::class;

    protected static ?string $slug = 'correspondences';

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $recordTitleAttribute = 'ref_number';

    protected static ?string $navigationGroup = 'Pre-Conference';

    protected static ?int $navigationSort = 120;

    public static function form(Form $form): Form
    {
        return CorrespondenceForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return CorrespondencesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCorrespondences::route('/'),
            'create' => CreateCorrespondence::route('/create'),
            'view' => ViewCorrespondence::route('/{record}'),
            'edit' => EditCorrespondence::route('/{record}/edit'),
        ];
    }
}
