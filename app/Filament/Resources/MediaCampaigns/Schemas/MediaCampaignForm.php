<?php

namespace App\Filament\Resources\MediaCampaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class MediaCampaignForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Campaign Details')
                    ->icon('heroicon-o-megaphone')
                    ->columns(2)
                    ->schema([
                        Select::make('conference_id')->label('Conference')->relationship('conference', 'title')->searchable()->preload()->required(),
                        TextInput::make('title')->label('Title')->required(),
                        Select::make('campaign_type')->label('Type')->options([
                            'pre_conference' => 'Pre Conference', 'during_conference' => 'During Conference', 'post_conference' => 'Post Conference',
                        ])->required(),
                        Select::make('status')->label('Status')->options(['planning' => 'Planning', 'active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])->required(),
                        Select::make('manager_user_id')->label('Manager')->relationship('manager', 'name')->searchable()->preload(),
                    ]),

                Section::make('Planning & Budget')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('press_conference_date')->label('Press Conference Date'),
                        TextInput::make('estimated_budget')->label('Estimated Budget')->numeric()->minValue(0),
                        TextInput::make('actual_cost')->label('Actual Cost')->numeric()->minValue(0),
                        Textarea::make('plan_text')->label('Plan')->columnSpanFull(),
                        Textarea::make('target_outlets')->label('Target Outlets')->columnSpanFull(),
                        Textarea::make('press_kit_notes')->label('Press Kit Notes')->columnSpanFull(),
                    ]),
            ]);
    }
}
