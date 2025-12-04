<?php

namespace App\Filament\Resources\Airports;

use App\Filament\Resources\Airports\Pages\CreateAirport;
use App\Filament\Resources\Airports\Pages\EditAirport;
use App\Filament\Resources\Airports\Pages\ListAirports;
use App\Filament\Resources\Airports\Schemas\AirportForm;
use App\Filament\Resources\Airports\Tables\AirportsTable;
use App\Models\Airport;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class AirportResource extends Resource
{
    protected static ?string $model = Airport::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $recordTitleAttribute = 'name_ar';

    protected static ?string $navigationGroup = 'Logistics';

    protected static ?int $navigationSort = 330;

    public static function form(Form $form): Form
    {
        return AirportForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return AirportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAirports::route('/'),
            'create' => CreateAirport::route('/create'),
            'edit' => EditAirport::route('/{record}/edit'),
        ];
    }
}
