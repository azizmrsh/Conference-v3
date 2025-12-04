<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

class TaskForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Task Details')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(2)
                    ->schema([
                        Select::make('conference_id')->label('Conference')->relationship('conference', 'title')->searchable()->preload(),
                        Select::make('category')->label('Category')->options([
                            'pre_conference' => 'Pre Conference',
                            'invitation_management' => 'Invitation Management',
                            'paper_management' => 'Paper Management',
                            'logistics' => 'Logistics',
                            'protocol' => 'Protocol',
                            'media_campaign' => 'Media Campaign',
                            'financial' => 'Financial',
                            'during_conference' => 'During Conference',
                            'post_conference' => 'Post Conference',
                        ])->required(),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('priority')->numeric()->minValue(1)->maxValue(5)->label('Priority'),
                    ]),

                Section::make('Description')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Textarea::make('description')->label('Description')->columnSpanFull(),
                    ]),

                Section::make('Assignment & Schedule')
                    ->icon('heroicon-o-calendar')
                    ->columns(2)
                    ->schema([
                        Select::make('assigned_to')->label('Assigned To')->relationship('assignee', 'name')->searchable()->preload(),
                        TextInput::make('department_responsible')->label('Department'),
                        DatePicker::make('start_date')->label('Start Date'),
                        DatePicker::make('due_date')->label('Due Date'),
                        TextInput::make('estimated_hours')->numeric()->minValue(0)->label('Estimated Hours'),
                    ]),

                Section::make('Status & Progress')
                    ->icon('heroicon-o-chart-pie')
                    ->columns(2)
                    ->schema([
                        Select::make('status')->label('Status')->options([
                            'not_started' => 'Not Started', 'in_progress' => 'In Progress', 'waiting' => 'Waiting', 'completed' => 'Completed', 'overdue' => 'Overdue', 'cancelled' => 'Cancelled',
                        ])->required(),
                        TextInput::make('completion_percentage')->numeric()->minValue(0)->maxValue(100)->label('Completion %'),
                    ]),

                Section::make('Requirements')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Textarea::make('prerequisites')->label('Prerequisites')->columnSpanFull(),
                        Textarea::make('deliverables')->label('Deliverables')->columnSpanFull(),
                        Textarea::make('success_criteria')->label('Success Criteria')->columnSpanFull(),
                    ]),

                Section::make('Reminders & Notes')
                    ->icon('heroicon-o-bell')
                    ->columns(2)
                    ->schema([
                        Toggle::make('auto_reminder')->label('Auto Reminder')->default(true),
                        TextInput::make('reminder_schedule')->label('Reminder Schedule'),
                        Textarea::make('notes')->label('Notes')->columnSpanFull(),
                        Select::make('created_by')->label('Created By')->relationship('creator', 'name')->searchable()->preload(),
                    ]),
            ]);
    }
}
