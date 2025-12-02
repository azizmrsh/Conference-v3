<?php

namespace App\Filament\Resources\Attachments\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class AttachmentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('File Details')
                    ->icon('heroicon-o-document')
                    ->columns(2)
                    ->schema([
                        TextInput::make('filename')->label('Filename'),
                        TextInput::make('path')->label('Path')->required(),
                        TextInput::make('disk')->label('Disk')->default('s3'),
                        TextInput::make('mime')->label('MIME'),
                        TextInput::make('size')->label('Size')->numeric()->minValue(0),
                    ]),

                Section::make('Association')
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        TextInput::make('attachable_type')->label('Attachable Type')->required(),
                        TextInput::make('attachable_id')->label('Attachable ID')->numeric()->required(),
                        TextInput::make('collection')->label('Collection')->required(),
                        Select::make('uploaded_by')->label('Uploaded By')->relationship('uploader','name')->searchable()->preload(),
                    ]),

                Section::make('Meta Data')
                    ->icon('heroicon-o-code-bracket')
                    ->schema([
                        Textarea::make('meta')->label('Meta')->columnSpanFull(),
                    ]),
            ]);
    }
}


