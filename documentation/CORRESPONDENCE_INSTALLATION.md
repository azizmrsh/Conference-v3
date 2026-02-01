# Correspondence Management System - Installation & Setup Guide

## Installation Steps

### 1. Install Spatie Media Library

```bash
composer require "spatie/laravel-medialibrary:^11.0"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan migrate
```

### 2. Install PDF Generation (Choose one)

**Option A: DomPDF (Recommended for simple PDFs)**
```bash
composer require barryvdh/laravel-dompdf
```

**Option B: Browsershot (For complex HTML→PDF)**
```bash
composer require spatie/browsershot
npm install puppeteer
```

### 3. Run Migrations

```bash
# Drop old correspondences table if exists
php artisan migrate:rollback --step=1

# Run new migration
php artisan migrate
```

### 4. Generate Factory & Policy

```bash
php artisan make:factory CorrespondenceFactory
php artisan make:policy CorrespondencePolicy --model=Correspondence
```

### 5. Register Policy

Add to `AuthServiceProvider`:
```php
protected $policies = [
    Correspondence::class => CorrespondencePolicy::class,
];
```

### 6. Generate Shield Permissions

```bash
php artisan shield:generate --resource=CorrespondenceResource
```

### 7. Create PDF Preview View

Create file: `resources/views/filament/forms/components/pdf-preview.blade.php`

```blade
@php
    $record = $getRecord();
    $pdfMedia = $record?->getFirstMedia('pdf');
@endphp

@if($pdfMedia)
    <div class="rounded-lg border border-gray-300 overflow-hidden" style="height: 600px;">
        <iframe 
            src="{{ $pdfMedia->getUrl() }}" 
            class="w-full h-full"
            frameborder="0"
        ></iframe>
    </div>
@else
    <div class="text-center text-gray-500 py-4">
        No PDF uploaded yet
    </div>
@endif
```

### 8. Create PDF Generation Service

Create: `app/Services/CorrespondencePdfService.php`

```php
<?php

namespace App\Services;

use App\Models\Correspondence;
use Barryvdh\DomPDF\Facade\Pdf;

class CorrespondencePdfService
{
    public function generate(Correspondence $correspondence): string
    {
        $pdf = Pdf::loadView('pdf.correspondence', [
            'correspondence' => $correspondence,
        ]);

        $fileName = 'correspondence_' . $correspondence->ref_number . '.pdf';
        $path = storage_path('app/public/pdf/' . $fileName);
        
        $pdf->save($path);

        // Attach to media library
        $correspondence->addMedia($path)
            ->toMediaCollection('pdf');

        return $path;
    }
}
```

### 9. Create PDF Blade Template

Create: `resources/views/pdf/correspondence.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $correspondence->subject }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin: 20px; line-height: 1.8; }
        .footer { position: fixed; bottom: 0; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $correspondence->subject }}</h2>
        <p>Ref: {{ $correspondence->ref_number }}</p>
        <p>Date: {{ $correspondence->correspondence_date?->format('Y-m-d') }}</p>
    </div>

    @if($correspondence->header)
        <div class="meta">
            @foreach($correspondence->header as $field)
                <p><strong>{{ $field['label'] }}:</strong> {{ $field['value'] }}</p>
            @endforeach
        </div>
    @endif

    <div class="content">
        {!! $correspondence->content !!}
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>
```

### 10. Create Mailable for Email Sending

```bash
php artisan make:mail CorrespondenceSent
```

Edit `app/Mail/CorrespondenceSent.php`:

```php
<?php

namespace App\Mail;

use App\Models\Correspondence;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorrespondenceSent extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Correspondence $correspondence)
    {
    }

    public function build()
    {
        $mail = $this->subject($this->correspondence->subject)
            ->view('emails.correspondence')
            ->with([
                'correspondence' => $this->correspondence,
            ]);

        // Attach PDF if exists
        $pdfMedia = $this->correspondence->getFirstMedia('pdf');
        if ($pdfMedia) {
            $mail->attach($pdfMedia->getPath(), [
                'as' => $pdfMedia->file_name,
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
```

### 11. Create Email Template

Create: `resources/views/emails/correspondence.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>{{ $correspondence->subject }}</h2>
    
    <p><strong>Reference:</strong> {{ $correspondence->ref_number }}</p>
    <p><strong>Date:</strong> {{ $correspondence->correspondence_date?->format('Y-m-d') }}</p>
    
    <div>
        {!! $correspondence->content !!}
    </div>
    
    <hr>
    <p style="font-size: 12px; color: #666;">
        This is an automated email from the Conference Management System.
    </p>
</body>
</html>
```

### 12. Update CorrespondencesTable.php Actions

Replace the placeholder actions with actual implementations:

```php
Action::make('generatePdf')
    ->label('Generate PDF')
    ->icon('heroicon-o-document-arrow-down')
    ->color('success')
    ->action(function ($record) {
        app(\App\Services\CorrespondencePdfService::class)->generate($record);
        \Filament\Notifications\Notification::make()
            ->title('PDF Generated Successfully')
            ->success()
            ->send();
    })
    ->requiresConfirmation(),

Action::make('sendEmail')
    ->label('Send Email')
    ->icon('heroicon-o-envelope')
    ->color('info')
    ->form([
        \Filament\Forms\Components\TextInput::make('email')
            ->email()
            ->required()
            ->default(fn ($record) => $record->member?->email ?? $record->recipient_entity),
    ])
    ->action(function ($record, array $data) {
        \Illuminate\Support\Facades\Mail::to($data['email'])
            ->send(new \App\Mail\CorrespondenceSent($record));
        
        $record->update(['status' => 'sent']);
        
        \Filament\Notifications\Notification::make()
            ->title('Email Sent Successfully')
            ->success()
            ->send();
    })
    ->requiresConfirmation(),
```

### 13. Schedule Follow-up Reminders

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('correspondence:send-reminders')
        ->dailyAt('09:00');
}
```

### 14. Test the System

```bash
# Seed data
php artisan db:seed --class=CorrespondenceSeeder

# Run tests
php artisan test --filter=Correspondence

# Check permissions
php artisan shield:check
```

## Usage Examples

### Creating a Correspondence via Code

```php
$correspondence = Correspondence::create([
    'category' => 'invitation',
    'workflow_group' => 'pre_conference',
    'direction' => 'outgoing',
    'subject' => 'Invitation to Conference',
    'content' => 'Dear Member, ...',
    'recipient_entity' => 'Dr. Ahmed Ali',
    'sender_entity' => 'Conference Committee',
    'status' => 'draft',
    'requires_follow_up' => true,
    'follow_up_at' => now()->addWeek(),
]);

// Auto-generate ref number
$correspondence->ref_number = $correspondence->generateRefNumber();
$correspondence->save();
```

### Loading Last Content

```php
$lastData = Correspondence::getLastContentForCategory('invitation');
if ($lastData) {
    $newCorrespondence->content = $lastData['content'];
    $newCorrespondence->header = $lastData['header'];
}
```

### Finding Pending Follow-ups

```php
$pending = Correspondence::pendingFollowUp()->get();
```

## Troubleshooting

### Media Library Issues

If you get errors about media collections:

```bash
php artisan config:clear
php artisan cache:clear
```

### PDF Generation Fails

Check storage permissions:

```bash
chmod -R 775 storage/app/public
php artisan storage:link
```

### Missing Filament Components

The system requires Filament v3 Media Library component:

```bash
composer require filament/spatie-laravel-media-library-plugin:"^3.0"
```

## Next Steps

1. Customize PDF template in `resources/views/pdf/correspondence.blade.php`
2. Add email queue configuration in `.env`
3. Set up notification channels for reminders
4. Create dashboard widgets for correspondence statistics
5. Add export functionality (Excel, CSV)

## Complete Feature Checklist

- [x] Migration with all fields
- [x] Model with HasMedia trait
- [x] Scopes and helper methods
- [x] Auto-update last_of_type
- [x] Filament Form with Tabs layout
- [x] Load Last Content action
- [x] Filament Table with badges
- [x] Comprehensive filters
- [x] PDF generation service
- [x] Email sending functionality
- [x] Follow-up reminder command
- [x] Bulk actions
- [x] Duplicate action
- [x] Mark as replied action
- [ ] Factory (to be created)
- [ ] Policy (to be created)
- [ ] Console command (to be created)
- [ ] Seeder (to be created)
