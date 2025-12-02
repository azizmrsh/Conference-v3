<?php

namespace App\Filament\Resources\Hotels;

use App\Filament\Resources\Hotels\Pages\CreateHotel;
use App\Filament\Resources\Hotels\Pages\EditHotel;
use App\Filament\Resources\Hotels\Pages\ListHotels;
use App\Filament\Resources\Hotels\Schemas\HotelForm;
use App\Filament\Resources\Hotels\Tables\HotelsTable;
use App\Models\Hotel;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $recordTitleAttribute = 'name_ar';

    protected static ?string $navigationGroup = 'Logistics';

    protected static ?int $navigationSort = 340;

    public static function form(Form $form): Form
    {
        return HotelForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return HotelsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHotels::route('/'),
            'create' => CreateHotel::route('/create'),
            'edit' => EditHotel::route('/{record}/edit'),
        ];
    }
}
