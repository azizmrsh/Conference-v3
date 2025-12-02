<?php

namespace App\Filament\Resources\Attachments;

use App\Filament\Resources\Attachments\Pages\CreateAttachment;
use App\Filament\Resources\Attachments\Pages\EditAttachment;
use App\Filament\Resources\Attachments\Pages\ListAttachments;
use App\Filament\Resources\Attachments\Schemas\AttachmentForm;
use App\Filament\Resources\Attachments\Tables\AttachmentsTable;
use App\Models\Attachment;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
class AttachmentResource extends Resource
{
    protected static ?string $model = Attachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    protected static ?string $recordTitleAttribute = 'filename';

    protected static ?string $navigationGroup = 'Pre-Conference';

    protected static ?int $navigationSort = 140;

    public static function form(Form $form): Form
    {
        return AttachmentForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return AttachmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttachments::route('/'),
            'create' => CreateAttachment::route('/create'),
            'edit' => EditAttachment::route('/{record}/edit'),
        ];
    }
}
