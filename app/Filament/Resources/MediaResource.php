<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Media & Archiving';

    protected static ?int $navigationSort = 510;

    protected static ?string $recordTitleAttribute = 'file_name';

    protected static ?string $modelLabel = 'Media File';

    protected static ?string $pluralModelLabel = 'Media Library';

    // إخفاء من القائمة الجانبية (سنستخدم Media Manager Plugin بدلاً منه)
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('File Information')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('file_name')
                            ->label('File Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('Custom Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('collection_name')
                            ->label('Collection')
                            ->options([
                                'attachments' => 'Attachments',
                                'generated_pdf' => 'Generated PDF',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('mime_type')
                            ->label('MIME Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('size')
                            ->label('File Size')
                            ->disabled()
                            ->suffix('bytes')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state).' bytes ('.number_format($state / 1024 / 1024, 2).' MB)' : '-'),
                        Forms\Components\TextInput::make('disk')
                            ->label('Storage Disk')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Model Association')
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('model_type')
                            ->label('Model Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('model_id')
                            ->label('Model ID')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Custom Properties')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed()
                    ->schema([
                        Forms\Components\KeyValue::make('custom_properties')
                            ->label('Properties')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_name')
                    ->label('Preview')
                    ->getStateUsing(function ($record) {
                        if (str_starts_with($record->mime_type, 'image/')) {
                            return $record->getUrl('thumb');
                        }

                        return null;
                    })
                    ->defaultImageUrl(fn ($record) => match (true) {
                        str_contains($record->mime_type, 'pdf') => asset('images/pdf-icon.svg'),
                        str_contains($record->mime_type, 'word') || str_contains($record->mime_type, 'document') => asset('images/doc-icon.svg'),
                        default => asset('images/file-icon.svg'),
                    })
                    ->circular()
                    ->size(60),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('File name copied!')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->file_name),

                Tables\Columns\TextColumn::make('name')
                    ->label('Custom Name')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('collection_name')
                    ->label('Collection')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'attachments' => 'info',
                        'generated_pdf' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('model_type')
                    ->label('Related To')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2).' KB')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('collection_name')
                    ->label('Collection')
                    ->options([
                        'attachments' => 'Attachments',
                        'generated_pdf' => 'Generated PDF',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('model_type')
                    ->label('Related Model')
                    ->options([
                        'App\\Models\\Correspondence' => 'Correspondence',
                        'App\\Models\\Conference' => 'Conference',
                        'App\\Models\\Member' => 'Member',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('images_only')
                    ->label('Images Only')
                    ->query(fn (Builder $query) => $query->where('mime_type', 'like', 'image/%')),

                Tables\Filters\Filter::make('pdfs_only')
                    ->label('PDFs Only')
                    ->query(fn (Builder $query) => $query->where('mime_type', 'application/pdf')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('uploaded_from')
                            ->label('Uploaded From'),
                        Forms\Components\DatePicker::make('uploaded_until')
                            ->label('Uploaded Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['uploaded_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['uploaded_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => $record->getUrl(), shouldOpenInNewTab: true),

                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalContent(fn ($record) => view('filament.resources.media.preview', ['record' => $record]))
                    ->modalWidth('4xl')
                    ->visible(fn ($record) => str_starts_with($record->mime_type, 'image/') || $record->mime_type === 'application/pdf'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'view' => Pages\ViewMedia::route('/{record}'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
