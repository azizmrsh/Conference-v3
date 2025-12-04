<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

class ReviewForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Assignment')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(3)
                    ->schema([
                        Select::make('paper_id')->label('Paper')->relationship('paper', 'title_ar')->searchable()->preload()->required(),
                        Select::make('reviewer_user_id')->label('Reviewer')->relationship('reviewer', 'name')->searchable()->preload(),
                        Select::make('review_type')->label('Type')->options([
                            'scientific_review' => 'Scientific', 'linguistic_review' => 'Linguistic', 'technical_review' => 'Technical', 'final_review' => 'Final',
                        ])->required(),
                        TextInput::make('review_round')->label('Round')->numeric()->minValue(1)->default(1),
                        DateTimePicker::make('assigned_at')->label('Assigned At')->required(),
                        DatePicker::make('due_date')->label('Due Date')->required(),
                        DateTimePicker::make('started_at')->label('Started At'),
                        DateTimePicker::make('completed_at')->label('Completed At'),
                    ]),
                Section::make('Evaluation Scores')
                    ->icon('heroicon-o-star')
                    ->columns(3)
                    ->schema([
                        TextInput::make('content_quality')->numeric()->minValue(0)->maxValue(10)->label('Content'),
                        TextInput::make('methodology_score')->numeric()->minValue(0)->maxValue(10)->label('Methodology'),
                        TextInput::make('language_quality')->numeric()->minValue(0)->maxValue(10)->label('Language'),
                        TextInput::make('formatting_score')->numeric()->minValue(0)->maxValue(10)->label('Formatting'),
                        TextInput::make('overall_score')->numeric()->minValue(0)->maxValue(10)->label('Overall'),
                    ]),

                Section::make('Final Verdict')
                    ->icon('heroicon-o-check-badge')
                    ->columns(2)
                    ->schema([
                        Select::make('recommendation')->label('Recommendation')->options(['accept' => 'Accept', 'accept_with_minor' => 'Accept w/ Minor', 'major_revision' => 'Major Revision', 'reject' => 'Reject'])->required(),
                        Select::make('status')->label('Status')->options(['assigned' => 'Assigned', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'overdue' => 'Overdue', 'cancelled' => 'Cancelled'])->required(),
                        Textarea::make('general_comments')->label('Comments')->columnSpanFull(),
                        TextInput::make('annotated_file_path')->label('Annotated File'),
                        Toggle::make('confidential')->label('Confidential')->default(true),
                        Select::make('created_by')->label('Created By')->relationship('creator', 'name')->searchable()->preload(),
                    ]),
            ]);
    }
}
