<?php

namespace App\Filament\Resources\Correspondences\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Validation\Rule;

class CorrespondenceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Reference')
                    ->columns(3)
                    ->schema([
                        Select::make('conference_id')->label('Conference')->relationship('conference', 'title')->searchable()->preload(),
                        TextInput::make('ref_number')->label('Reference No.'),
                        DatePicker::make('correspondence_date')->label('Date'),
                    ]),
                Section::make('Classification')
                    ->columns(3)
                    ->schema([
                        Select::make('direction')->label('Direction')->options(['outgoing'=>'Outgoing','incoming'=>'Incoming'])->required()->rule(Rule::in(['outgoing','incoming'])),
                        Select::make('category')->label('Category')->options(['general'=>'General','royal'=>'Royal','diplomatic'=>'Diplomatic','press'=>'Press','internal'=>'Internal'])->required(),
                        Select::make('status')->label('Status')->options(['draft'=>'Draft','sent'=>'Sent','received'=>'Received','replied'=>'Replied','approved'=>'Approved','rejected'=>'Rejected','pending'=>'Pending'])->required(),
                    ]),
                Section::make('Recipient & Subject')
                    ->columns(2)
                    ->schema([
                        TextInput::make('recipient_entity')->label('Recipient'),
                        Textarea::make('subject')->label('Subject')->columnSpanFull(),
                    ]),
                Section::make('Content')
                    ->columns(1)
                    ->schema([
                        Textarea::make('content')->label('Content'),
                        TextInput::make('file_path')->label('File Path'),
                    ]),
                Section::make('Follow-up')
                    ->columns(3)
                    ->schema([
                        Toggle::make('response_received')->label('Response Received')->default(false),
                        DatePicker::make('response_date')->label('Response Date'),
                        Toggle::make('requires_follow_up')->label('Requires Follow-up')->default(true),
                        DatePicker::make('follow_up_date')->label('Follow-up Date'),
                        TextInput::make('priority')->numeric()->minValue(1)->maxValue(5)->label('Priority'),
                    ]),
                Section::make('Meta')
                    ->columns(2)
                    ->schema([
                        Select::make('created_by')->label('Created By')->relationship('creator','name')->searchable()->preload(),
                        Textarea::make('notes')->label('Notes')->columnSpanFull(),
                    ]),
            ]);
    }
}


