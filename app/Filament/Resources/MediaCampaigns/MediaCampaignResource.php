<?php

namespace App\Filament\Resources\MediaCampaigns;

use App\Filament\Resources\MediaCampaigns\Pages\CreateMediaCampaign;
use App\Filament\Resources\MediaCampaigns\Pages\EditMediaCampaign;
use App\Filament\Resources\MediaCampaigns\Pages\ListMediaCampaigns;
use App\Filament\Resources\MediaCampaigns\Schemas\MediaCampaignForm;
use App\Filament\Resources\MediaCampaigns\Tables\MediaCampaignsTable;
use App\Models\MediaCampaign;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MediaCampaignResource extends Resource
{
    protected static ?string $model = MediaCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationGroup = 'Media & Archiving';

    protected static ?int $navigationSort = 510;

    public static function form(Form $form): Form
    {
        return MediaCampaignForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return MediaCampaignsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaCampaigns::route('/'),
            'create' => CreateMediaCampaign::route('/create'),
            'edit' => EditMediaCampaign::route('/{record}/edit'),
        ];
    }
}
