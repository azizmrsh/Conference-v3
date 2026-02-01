<?php

namespace App\Filament\Resources\Correspondences\Tables;

use App\Mail\CorrespondenceSent;
use App\Services\CorrespondencePdfService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class CorrespondencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ref_number')
                    ->label('Ref No.')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),

                TextColumn::make('conference.title')
                    ->label('Conference')
                    ->searchable()
                    ->toggleable()
                    ->limit(30),

                TextColumn::make('category')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'invitation' => 'success',
                        'member_consultation' => 'info',
                        'research' => 'primary',
                        'attendance' => 'warning',
                        'logistics' => 'gray',
                        'finance' => 'danger',
                        'royal_court' => 'purple',
                        'diplomatic' => 'indigo',
                        'security' => 'red',
                        'press' => 'cyan',
                        'membership' => 'lime',
                        'thanks' => 'pink',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),

                TextColumn::make('direction')
                    ->badge()
                    ->icon(fn (string $state): string => $state === 'outgoing' ? 'heroicon-o-arrow-up-tray' : 'heroicon-o-arrow-down-tray')
                    ->color(fn (string $state): string => $state === 'outgoing' ? 'success' : 'info'),

                TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'delivered' => 'info',
                        'received' => 'primary',
                        'replied' => 'success',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('workflow_group')
                    ->label('Workflow')
                    ->badge()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),

                TextColumn::make('sender_name')
                    ->searchable()
                    ->toggleable()
                    ->limit(25),

                TextColumn::make('recipient_name')
                    ->searchable()
                    ->toggleable()
                    ->limit(25),

                TextColumn::make('correspondence_date')
                    ->date()
                    ->label('Date')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('requires_follow_up')
                    ->label('Follow-up')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('follow_up_at')
                    ->label('Follow-up Date')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'gray'),

                TextColumn::make('member.full_name')
                    ->label('Member')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                IconColumn::make('attachments_count')
                    ->label('Files')
                    ->icon(fn ($record) => $record->hasAttachments() ? 'heroicon-o-paper-clip' : 'heroicon-o-x-mark')
                    ->color(fn ($record) => $record->hasAttachments() ? 'success' : 'gray')
                    ->tooltip(fn ($record) => $record->hasAttachments() ? $record->getAttachmentsCount().' file(s)' : 'No attachments')
                    ->toggleable(),

                IconColumn::make('has_pdf')
                    ->label('PDF')
                    ->boolean()
                    ->state(fn ($record) => $record->hasPdf())
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->defaultSort('correspondence_date', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->multiple()
                    ->searchable()
                    ->options([
                        'invitation' => 'Invitation',
                        'member_consultation' => 'Member Consultation',
                        'research' => 'Research',
                        'attendance' => 'Attendance',
                        'logistics' => 'Logistics',
                        'finance' => 'Finance',
                        'royal_court' => 'Royal Court',
                        'diplomatic' => 'Diplomatic',
                        'security' => 'Security',
                        'press' => 'Press',
                        'membership' => 'Membership',
                        'thanks' => 'Thanks',
                        'general' => 'General',
                    ]),

                SelectFilter::make('workflow_group')
                    ->label('Workflow')
                    ->multiple()
                    ->options([
                        'pre_conference' => 'Pre-Conference',
                        'scientific' => 'Scientific',
                        'logistics' => 'Logistics',
                        'media' => 'Media',
                        'finance' => 'Finance',
                        'membership' => 'Membership',
                        'royal' => 'Royal',
                        'security' => 'Security',
                        'general_ops' => 'General Operations',
                    ]),

                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'delivered' => 'Delivered',
                        'received' => 'Received',
                        'replied' => 'Replied',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'pending' => 'Pending',
                        'archived' => 'Archived',
                    ]),

                SelectFilter::make('direction')
                    ->options([
                        'outgoing' => 'Outgoing',
                        'incoming' => 'Incoming',
                    ]),

                Filter::make('requires_follow_up')
                    ->label('Requires Follow-up')
                    ->query(fn (Builder $query): Builder => $query->where('requires_follow_up', true)),

                Filter::make('pending_follow_up')
                    ->label('Pending Follow-up')
                    ->query(fn (Builder $query): Builder => $query->pendingFollowUp()),

                Filter::make('overdue_follow_up')
                    ->label('Overdue Follow-up')
                    ->query(fn (Builder $query): Builder => $query->where('requires_follow_up', true)
                        ->where('follow_up_at', '<', now())
                        ->where('status', '!=', 'replied')),

                Filter::make('correspondence_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From Date'),
                        Forms\Components\DatePicker::make('until')->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('correspondence_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('correspondence_date', '<=', $date),
                            );
                    }),

                SelectFilter::make('conference_id')
                    ->label('Conference')
                    ->relationship('conference', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('member_id')
                    ->label('Member')
                    ->relationship('member', 'full_name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                ReplicateAction::make()
                    ->excludeAttributes(['ref_number']),

                Action::make('downloadAttachments')
                    ->label('Download Files')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn ($record) => $record->hasAttachments())
                    ->url(fn ($record) => $record->getFirstMedia('attachments')?->getUrl())
                    ->openUrlInNewTab(),

                Action::make('viewPdf')
                    ->label('View PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => $record->hasPdf())
                    ->url(fn ($record) => $record->latestPdf())
                    ->openUrlInNewTab(),

                Action::make('generatePdf')
                    ->label('Generate PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        $pdfService = new CorrespondencePdfService;
                        $pdfService->generatePdf($record);

                        $media = $record->getFirstMedia('generated_pdf');

                        Notification::make()
                            ->title('PDF Generated')
                            ->success()
                            ->send();

                        if ($media) {
                            return response()->download($media->getPath(), $media->file_name);
                        }
                    }),

                Action::make('sendEmail')
                    ->label('Send Email')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('to_email')
                            ->label('Recipient Email')
                            ->email()
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $pdfService = new CorrespondencePdfService;
                        $pdfService->generatePdf($record);

                        Mail::to($data['to_email'])->send(new CorrespondenceSent($record));

                        $record->update(['status' => 'sent']);

                        Notification::make()
                            ->title('Email Sent')
                            ->success()
                            ->send();
                    }),

                Action::make('markReplied')
                    ->label('Mark Replied')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status !== 'replied')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'replied',
                            'response_received' => true,
                            'response_date' => now(),
                        ]);

                        Notification::make()
                            ->title('Marked as Replied')
                            ->success()
                            ->send();
                    }),

                Action::make('markCompleted')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(function ($record) {
                        $record->update(['status' => 'archived']);

                        Notification::make()
                            ->title('Archived')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('changeStatus')
                        ->label('Change Status')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'sent' => 'Sent',
                                    'delivered' => 'Delivered',
                                    'received' => 'Received',
                                    'replied' => 'Replied',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    'pending' => 'Pending',
                                    'archived' => 'Archived',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['status' => $data['status']]);

                            Notification::make()
                                ->title('Status Updated')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('exportPdfs')
                        ->label('Export PDFs')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            $pdfService = new CorrespondencePdfService;

                            foreach ($records as $record) {
                                $pdfService->generatePdf($record);
                            }

                            Notification::make()
                                ->title('PDFs Generated')
                                ->body(count($records).' PDFs created')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
