<?php

namespace App\Filament\Resources\TravelBookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class TravelBookingForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Booking Details')
                    ->icon('heroicon-o-ticket')
                    ->columns(2)
                    ->schema([
                        Select::make('invitation_id')
                            ->label('Invitation')
                            ->relationship('invitation', 'id')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'arrival_flight' => 'رحلة وصول',
                                'departure_flight' => 'رحلة مغادرة',
                                'hotel' => 'فندق',
                                'transfer' => 'نقل',
                            ])
                            ->required()
                            ->reactive(),
                        TextInput::make('cost')->label('Cost')->numeric()->minValue(0),
                        Select::make('booking_status')
                            ->label('Booking Status')
                            ->options([
                                'pending' => 'معلق',
                                'booked' => 'محجوز',
                                'issued' => 'مُصدر',
                                'cancelled' => 'ملغي',
                            ])
                            ->required(),
                    ]),

                Section::make('Flight Information')
                    ->icon('heroicon-o-paper-airplane')
                    ->columns(2)
                    ->visible(fn ($get) => in_array($get('type'), ['arrival_flight', 'departure_flight']))
                    ->schema([
                        Select::make('airline_id')
                            ->label('Airline')
                            ->relationship('airline', 'name_ar')
                            ->searchable()
                            ->preload(),
                        TextInput::make('flight_number')->label('Flight Number'),
                        DatePicker::make('flight_date')->label('Flight Date'),
                        DateTimePicker::make('flight_time')->label('Flight Time'),
                        Select::make('airport_from_id')
                            ->label('Departure Airport')
                            ->relationship('airportFrom', 'name_ar')
                            ->searchable()
                            ->preload(),
                        Select::make('airport_to_id')
                            ->label('Arrival Airport')
                            ->relationship('airportTo', 'name_ar')
                            ->searchable()
                            ->preload(),
                        TextInput::make('ticket_number')->label('Ticket Number'),
                        TextInput::make('ticket_path')->label('Ticket Path'),
                    ]),

                Section::make('Hotel Information')
                    ->icon('heroicon-o-building-office')
                    ->columns(2)
                    ->visible(fn ($get) => $get('type') === 'hotel')
                    ->schema([
                        Select::make('hotel_id')
                            ->label('Hotel')
                            ->relationship('hotel', 'name_ar')
                            ->searchable()
                            ->preload(),
                        Select::make('room_type')
                            ->label('Room Type')
                            ->options([
                                'standard' => 'عادية',
                                'suite' => 'سويت',
                                'royal_suite' => 'رويال سويت',
                            ]),
                        DatePicker::make('check_in')->label('Hotel Check-in'),
                        DatePicker::make('check_out')->label('Hotel Check-out'),
                    ]),

                Section::make('Delivery')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        Select::make('delivery_method')
                            ->label('Delivery Method')
                            ->options([
                                'email' => 'بريد إلكتروني',
                                'fax' => 'فاكس',
                                'hand' => 'باليد',
                                'courier' => 'بريد سريع',
                            ]),
                        DateTimePicker::make('delivery_confirmed_at')->label('Delivery Confirmed At'),
                    ]),
            ]);
    }
}
