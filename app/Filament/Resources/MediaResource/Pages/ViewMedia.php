<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewMedia extends ViewRecord
{
    protected static string $resource = MediaResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('File Preview')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Infolists\Components\View::make('filament.resources.media.preview')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('File Information')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('file_name')
                            ->label('File Name')
                            ->copyable()
                            ->icon('heroicon-o-document'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Custom Name')
                            ->icon('heroicon-o-tag'),
                        Infolists\Components\TextEntry::make('collection_name')
                            ->label('Collection')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'attachments' => 'info',
                                'generated_pdf' => 'success',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('mime_type')
                            ->label('MIME Type')
                            ->badge(),
                        Infolists\Components\TextEntry::make('size')
                            ->label('File Size')
                            ->formatStateUsing(fn ($state) => number_format($state / 1024, 2).' KB ('.number_format($state / 1024 / 1024, 2).' MB)')
                            ->icon('heroicon-o-circle-stack'),
                        Infolists\Components\TextEntry::make('disk')
                            ->label('Storage Disk')
                            ->badge()
                            ->color('warning'),
                    ]),

                Infolists\Components\Section::make('Model Association')
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('model_type')
                            ->label('Related Model')
                            ->formatStateUsing(fn ($state) => class_basename($state))
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('model_id')
                            ->label('Model ID'),
                    ]),

                Infolists\Components\Section::make('File Path')
                    ->icon('heroicon-o-folder')
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('getPath')
                            ->label('Relative Path')
                            ->copyable()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('getUrl')
                            ->label('Public URL')
                            ->copyable()
                            ->url(fn ($record) => $record->getUrl(), shouldOpenInNewTab: true)
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Custom Properties')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed()
                    ->visible(fn ($record) => ! empty($record->custom_properties))
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('custom_properties')
                            ->label('Properties')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Conversions')
                    ->icon('heroicon-o-arrows-pointing-out')
                    ->collapsed()
                    ->visible(fn ($record) => $record->hasGeneratedConversions())
                    ->schema([
                        Infolists\Components\TextEntry::make('generated_conversions')
                            ->label('Available Conversions')
                            ->formatStateUsing(function ($record) {
                                if (! $record->hasGeneratedConversions()) {
                                    return 'No conversions generated';
                                }
                                $conversions = $record->generated_conversions;

                                return implode(', ', array_keys($conversions));
                            })
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Timestamps')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download')
                ->label('Download File')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn () => $this->record->getUrl(), shouldOpenInNewTab: true),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
