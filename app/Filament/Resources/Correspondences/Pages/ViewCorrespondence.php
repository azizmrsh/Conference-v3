<?php

namespace App\Filament\Resources\Correspondences\Pages;

use App\Filament\Resources\Correspondences\CorrespondenceResource;
use App\Mail\CorrespondenceSent;
use App\Services\CorrespondencePdfService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ViewCorrespondence extends ViewRecord
{
    protected static string $resource = CorrespondenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('generatePdf')
                ->label('Generate PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $pdfService = new CorrespondencePdfService();
                    $pdfPath = $pdfService->generatePdf($this->record);

                    Notification::make()
                        ->title('PDF Generated Successfully')
                        ->success()
                        ->send();

                    return response()->download(storage_path('app/public/' . $pdfPath));
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
                    $ccEmails = !empty($data['cc_emails'])
                        ? array_map('trim', explode(',', $data['cc_emails']))
                        : [];

                    // Generate PDF first
                    $pdfService = new CorrespondencePdfService();
                    $pdfPath = $pdfService->generatePdf($this->record);

                    // Send email
                    $mailable = new CorrespondenceSent(
                        $this->record,
                        $data['additional_message'] ?? null
                    );

                    if (!empty($ccEmails)) {
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
