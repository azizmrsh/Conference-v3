<?php

namespace App\Filament\Resources\PressReleases\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class PressReleaseForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Press Release')
                    ->icon('heroicon-o-newspaper')
                    ->columns(2)
                    ->schema([
                        Select::make('media_campaign_id')->label('Media Campaign')->relationship('mediaCampaign','title')->searchable()->preload()->required(),
                        TextInput::make('title')->label('Title')->required(),
                        Textarea::make('content')->label('Content')->columnSpanFull()->required(),
                        Select::make('release_type')->label('Release Type')->options([
                            'announcement'=>'Announcement','invitation'=>'Invitation','daily_coverage'=>'Daily Coverage','final_statement'=>'Final Statement','follow_up'=>'Follow Up'
                        ])->required(),
                        DateTimePicker::make('scheduled_release_time')->label('Scheduled'),
                        DateTimePicker::make('actual_release_time')->label('Actual'),
                        Select::make('status')->label('Status')->options(['draft'=>'Draft','approved'=>'Approved','sent'=>'Sent','published'=>'Published'])->required(),
                        Select::make('created_by')->label('Created By')->relationship('creator','name')->searchable()->preload(),
                    ]),
            ]);
    }
}


