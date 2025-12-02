<?php

namespace App\Filament\Resources\Reviews;

use App\Filament\Resources\Reviews\Pages\CreateReview;
use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Reviews\Schemas\ReviewForm;
use App\Filament\Resources\Reviews\Tables\ReviewsTable;
use App\Models\Review;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationGroup = 'Scientific Committee';

    protected static ?int $navigationSort = 220;

    public static function form(Form $form): Form
    {
        return ReviewForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ReviewsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
