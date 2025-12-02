<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class TransactionForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Transaction Details')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        Select::make('conference_id')->label('Conference')->relationship('conference','title_ar')->searchable()->preload()->required(),
                        Select::make('tx_type')->label('Type')->options(['budget_item'=>'Budget Item','expense'=>'Expense','payment'=>'Payment'])->required(),
                        TextInput::make('category')->label('Category'),
                        TextInput::make('item_name')->label('Item Name')->required(),
                    ]),

                Section::make('Financials')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        TextInput::make('estimated_amount')->label('Estimated')->numeric()->minValue(0),
                        TextInput::make('actual_amount')->label('Actual')->numeric()->minValue(0),
                        DatePicker::make('incurred_at')->label('Date'),
                        Select::make('status')->label('Status')->options(['pending'=>'Pending','paid'=>'Paid','cancelled'=>'Cancelled'])->required(),
                    ]),

                Section::make('Notes')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        Textarea::make('notes')->label('Notes')->columnSpanFull(),
                        Select::make('created_by')->label('Created By')->relationship('creator','name')->searchable()->preload(),
                    ]),
            ]);
    }
}


