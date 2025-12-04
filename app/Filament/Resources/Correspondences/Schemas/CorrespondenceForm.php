<?php

namespace App\Filament\Resources\Correspondences\Schemas;

use App\Models\Correspondence;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;

class CorrespondenceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Correspondence Details')
                    ->tabs([
                        Tabs\Tab::make('General Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Classification')
                                    ->columns(3)
                                    ->schema([
                                        Select::make('direction')
                                            ->label('Direction')
                                            ->options([
                                                'outgoing' => 'Outgoing',
                                                'incoming' => 'Incoming',
                                            ])
                                            ->default('outgoing')
                                            ->required()
                                            ->live(),

                                        Select::make('category')
                                            ->label('Category')
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
                                            ])
                                            ->default('general')
                                            ->required()
                                            ->searchable()
                                            ->live(),

                                        Select::make('workflow_group')
                                            ->label('Workflow Group')
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
                                            ])
                                            ->default('general_ops')
                                            ->required()
                                            ->searchable(),
                                    ]),

                                Section::make('References')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('conference_id')
                                            ->label('Conference')
                                            ->relationship('conference', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('title')->required(),
                                            ]),

                                        Select::make('member_id')
                                            ->label('Member')
                                            ->relationship('member', 'full_name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')->required(),
                                            ]),

                                        TextInput::make('ref_number')
                                            ->label('Reference Number')
                                            ->placeholder('Auto-generated if empty')
                                            ->suffixAction(
                                                Action::make('generate')
                                                    ->icon('heroicon-o-sparkles')
                                                    ->action(function (Set $set, Get $get) {
                                                        $category = $get('category') ?? 'general';
                                                        $temp = new Correspondence(['category' => $category]);
                                                        $set('ref_number', $temp->generateRefNumber());
                                                    })
                                            ),

                                        DatePicker::make('correspondence_date')
                                            ->label('Correspondence Date')
                                            ->default(now())
                                            ->native(false)
                                            ->displayFormat('d/m/Y'),
                                    ]),

                                Section::make('Parties')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('sender_entity')
                                            ->label('Sender Entity')
                                            ->default('Conference Organizing Committee')
                                            ->maxLength(255),

                                        TextInput::make('recipient_entity')
                                            ->label('Recipient Entity')
                                            ->maxLength(255),
                                    ]),

                                Section::make('Status & Priority')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status')
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
                                            ->default('draft')
                                            ->required()
                                            ->live(),

                                        TextInput::make('priority')
                                            ->label('Priority (1-5)')
                                            ->numeric()
                                            ->default(3)
                                            ->minValue(1)
                                            ->maxValue(5)
                                            ->helperText('1 = Highest, 5 = Lowest'),

                                        Toggle::make('response_received')
                                            ->label('Response Received')
                                            ->live(),

                                        DatePicker::make('response_date')
                                            ->label('Response Date')
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->visible(fn (Get $get) => $get('response_received')),
                                    ]),
                            ]),

                        Tabs\Tab::make('Message Body')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Fieldset::make('Header Information')
                                    ->schema([
                                        Repeater::make('header')
                                            ->label('Header Fields')
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('Label')
                                                    ->required(),
                                                TextInput::make('value')
                                                    ->label('Value')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Header Field'),
                                    ]),

                                TextInput::make('subject')
                                    ->label('Subject')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                RichEditor::make('content')
                                    ->label('Content')
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ])
                                    ->columnSpanFull()
                                    ->fileAttachmentsDirectory('correspondence-attachments')
                                    ->hintAction(
                                        Action::make('loadLastContent')
                                            ->label('Load Last Content')
                                            ->icon('heroicon-o-arrow-path')
                                            ->action(function (Set $set, Get $get) {
                                                $category = $get('category');
                                                if (!$category) {
                                                    return;
                                                }

                                                $lastData = Correspondence::getLastContentForCategory($category);
                                                if ($lastData) {
                                                    $set('content', $lastData['content']);
                                                    $set('header', $lastData['header']);
                                                    if ($lastData['sender_entity']) {
                                                        $set('sender_entity', $lastData['sender_entity']);
                                                    }
                                                }
                                            })
                                            ->requiresConfirmation()
                                            ->modalHeading('Load Last Content')
                                            ->modalDescription('This will replace the current content with the last correspondence of this category.')
                                    ),

                                Textarea::make('notes')
                                    ->label('Internal Notes')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Follow-up')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Toggle::make('requires_follow_up')
                                    ->label('Requires Follow-up')
                                    ->default(true)
                                    ->live()
                                    ->columnSpanFull(),

                                DateTimePicker::make('follow_up_at')
                                    ->label('Follow-up Date & Time')
                                    ->native(false)
                                    ->visible(fn (Get $get) => $get('requires_follow_up'))
                                    ->suffixActions([
                                        Action::make('add1Day')
                                            ->label('+1 Day')
                                            ->icon('heroicon-o-plus-circle')
                                            ->action(fn (Set $set) => $set('follow_up_at', now()->addDay())),
                                        Action::make('add1Week')
                                            ->label('+1 Week')
                                            ->icon('heroicon-o-plus-circle')
                                            ->action(fn (Set $set) => $set('follow_up_at', now()->addWeek())),
                                        Action::make('add1Month')
                                            ->label('+1 Month')
                                            ->icon('heroicon-o-plus-circle')
                                            ->action(fn (Set $set) => $set('follow_up_at', now()->addMonth())),
                                    ]),
                            ]),

                        Tabs\Tab::make('Attachments & PDF')
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('attachments')
                                    ->label('Attachments')
                                    ->collection('attachments')
                                    ->multiple()
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),

                                SpatieMediaLibraryFileUpload::make('pdf')
                                    ->label('PDF Document')
                                    ->collection('pdf')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),

                                ViewField::make('pdf_preview')
                                    ->label('PDF Preview')
                                    ->view('filament.forms.components.pdf-preview')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => $record?->getFirstMedia('pdf') !== null),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

