<?php

namespace App\Filament\Resources\Correspondences\Pages;

use App\Filament\Resources\Conferences\ConferenceResource;
use App\Filament\Resources\Correspondences\CorrespondenceResource;
use App\Filament\Resources\Members\MemberResource;
use App\Mail\CorrespondenceSent;
use App\Services\CorrespondencePdfService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewCorrespondence extends ViewRecord
{
    protected static string $resource = CorrespondenceResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Basic Information')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('reference_number')
                            ->label('Reference Number')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'draft' => 'gray',
                                'sent' => 'info',
                                'received' => 'warning',
                                'replied' => 'success',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'pending' => 'warning',
                                default => 'gray'
                            }),
                        Infolists\Components\TextEntry::make('subject')
                            ->label('Subject')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('direction')
                            ->label('Direction')
                            ->badge()
                            ->color(fn ($state) => $state === 'outgoing' ? 'success' : 'info'),
                        Infolists\Components\TextEntry::make('correspondence_date')
                            ->label('Correspondence Date')
                            ->date(),
                        Infolists\Components\TextEntry::make('sender')
                            ->label('Sender')
                            ->visible(fn ($record) => $record->direction === 'incoming'),
                        Infolists\Components\TextEntry::make('recipient')
                            ->label('Recipient')
                            ->visible(fn ($record) => $record->direction === 'outgoing'),
                    ]),

                Infolists\Components\Section::make('Related Information')
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('conference.title')
                            ->label('Conference')
                            ->placeholder('N/A')
                            ->url(fn ($record) => $record->conference_id ? ConferenceResource::getUrl('view', ['record' => $record->conference_id]) : null),
                        Infolists\Components\TextEntry::make('member.full_name')
                            ->label('Member')
                            ->placeholder('N/A')
                            ->url(fn ($record) => $record->member_id ? MemberResource::getUrl('edit', ['record' => $record->member_id]) : null),
                    ]),

                Infolists\Components\Section::make('Content')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Infolists\Components\TextEntry::make('content')
                            ->label('Message Content')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Attachments')
                    ->icon('heroicon-o-paper-clip')
                    ->visible(fn ($record) => $record->hasAttachments())
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('attachments')
                            ->label('Uploaded Files')
                            ->collection('attachments')
                            ->conversion('preview')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Generated PDF')
                    ->icon('heroicon-o-document-text')
                    ->visible(fn ($record) => $record->hasPdf())
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('generated_pdf')
                            ->label('PDF Preview')
                            ->collection('generated_pdf')
                            ->conversion('preview')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Response Details')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->columns(2)
                    ->visible(fn ($record) => $record->response_received)
                    ->schema([
                        Infolists\Components\IconEntry::make('response_received')
                            ->label('Response Received')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('response_date')
                            ->label('Response Date')
                            ->date(),
                        Infolists\Components\TextEntry::make('response_content')
                            ->label('Response Content')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Tracking Information')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('Created By'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updater.name')
                            ->label('Last Updated By')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('last_sent_at')
                            ->label('Last Sent At')
                            ->dateTime()
                            ->placeholder('N/A'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('generatePdf')
                ->label('Generate PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $pdfService = new CorrespondencePdfService;
                    $pdfService->generatePdf($this->record);

                    $media = $this->record->getFirstMedia('generated_pdf');

                    Notification::make()
                        ->title('PDF Generated Successfully')
                        ->success()
                        ->send();

                    if ($media) {
                        return response()->download($media->getPath(), $media->file_name);
                    }
                }),

            Actions\Action::make('sendEmail')
                ->label('Send Email')
                ->icon('heroicon-o-envelope')
                ->color('primary')
                ->form([
                    Forms\Components\TextInput::make('to_email')
                        ->label('Recipient Email')
                        ->email()
                        ->required()
                        ->default(function () {
                            // Pre-fill with member email if available
                            if ($this->record->member) {
                                return $this->record->member->email;
                            }

                            return null;
                        }),
                    Forms\Components\TextInput::make('cc_emails')
                        ->label('CC Emails (comma separated)')
                        ->placeholder('email1@example.com, email2@example.com'),
                    Forms\Components\Textarea::make('additional_message')
                        ->label('Additional Message')
                        ->rows(3)
                        ->placeholder('Optional message to include in the email'),
                ])
                ->action(function (array $data) {
                    $toEmail = $data['to_email'];
                    $ccEmails = ! empty($data['cc_emails'])
                        ? array_map('trim', explode(',', $data['cc_emails']))
                        : [];

                    // Generate PDF first
                    $pdfService = new CorrespondencePdfService;
                    $pdfPath = $pdfService->generatePdf($this->record);

                    // Send email
                    $mailable = new CorrespondenceSent(
                        $this->record,
                        $data['additional_message'] ?? null
                    );

                    if (! empty($ccEmails)) {
                        $mailable->cc($ccEmails);
                    }

                    Mail::to($toEmail)->send($mailable);

                    // Update correspondence status
                    $this->record->update([
                        'status' => 'sent',
                        'last_sent_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Email Sent Successfully')
                        ->body("Email sent to {$toEmail}")
                        ->success()
                        ->send();
                }),

            Actions\Action::make('markReplied')
                ->label('Mark as Replied')
                ->icon('heroicon-o-check-circle')
                ->color('warning')
                ->visible(fn () => $this->record->status !== 'replied')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'replied',
                        'response_received' => true,
                        'response_date' => now(),
                    ]);

                    Notification::make()
                        ->title('Marked as Replied')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
