<?php

namespace App\Filament\Resources\PressReleases;

use App\Filament\Resources\PressReleases\Pages\CreatePressRelease;
use App\Filament\Resources\PressReleases\Pages\EditPressRelease;
use App\Filament\Resources\PressReleases\Pages\ListPressReleases;
use App\Filament\Resources\PressReleases\Schemas\PressReleaseForm;
use App\Filament\Resources\PressReleases\Tables\PressReleasesTable;
use App\Models\PressRelease;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PressReleaseResource extends Resource
{
    protected static ?string $model = PressRelease::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationGroup = 'Media & Archiving';

    protected static ?int $navigationSort = 520;

    public static function form(Form $form): Form
    {
        return PressReleaseForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return PressReleasesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPressReleases::route('/'),
            'create' => CreatePressRelease::route('/create'),
            'edit' => EditPressRelease::route('/{record}/edit'),
        ];
    }
}
