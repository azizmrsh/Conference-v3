<?php

namespace App\Filament\Resources\Airlines;

use App\Filament\Resources\Airlines\Pages\CreateAirline;
use App\Filament\Resources\Airlines\Pages\EditAirline;
use App\Filament\Resources\Airlines\Pages\ListAirlines;
use App\Filament\Resources\Airlines\Schemas\AirlineForm;
use App\Filament\Resources\Airlines\Tables\AirlinesTable;
use App\Models\Airline;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
class AirlineResource extends Resource
{
    protected static ?string $model = Airline::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $recordTitleAttribute = 'name_ar';

    protected static ?string $navigationGroup = 'Logistics';

    protected static ?int $navigationSort = 320;

    public static function form(Form $form): Form
    {
        return AirlineForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return AirlinesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAirlines::route('/'),
            'create' => CreateAirline::route('/create'),
            'edit' => EditAirline::route('/{record}/edit'),
        ];
    }
}
