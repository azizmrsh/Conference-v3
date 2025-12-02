<?php

namespace App\Filament\Resources\BadgesKits;

use App\Filament\Resources\BadgesKits\Pages\CreateBadgesKit;
use App\Filament\Resources\BadgesKits\Pages\EditBadgesKit;
use App\Filament\Resources\BadgesKits\Pages\ListBadgesKits;
use App\Filament\Resources\BadgesKits\Schemas\BadgesKitForm;
use App\Filament\Resources\BadgesKits\Tables\BadgesKitsTable;
use App\Models\BadgesKit;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
class BadgesKitResource extends Resource
{
    protected static ?string $model = BadgesKit::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $recordTitleAttribute = 'item_type';

    protected static ?string $navigationGroup = 'Media & Archiving';

    protected static ?int $navigationSort = 530;

    public static function form(Form $form): Form
    {
        return BadgesKitForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return BadgesKitsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBadgesKits::route('/'),
            'create' => CreateBadgesKit::route('/create'),
            'edit' => EditBadgesKit::route('/{record}/edit'),
        ];
    }
}
