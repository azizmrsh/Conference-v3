#!/usr/bin/env pwsh
# Correspondence Management System - Automated Setup Script

Write-Host "🚀 Setting up Correspondence Management System..." -ForegroundColor Cyan

# Create necessary directories
Write-Host "`n📁 Creating directories..." -ForegroundColor Yellow
New-Item -ItemType Directory -Force -Path "app/Services" | Out-Null
New-Item -ItemType Directory -Force -Path "app/Mail" | Out-Null
New-Item -ItemType Directory -Force -Path "resources/views/pdf" | Out-Null
New-Item -ItemType Directory -Force -Path "resources/views/emails" | Out-Null
New-Item -ItemType Directory -Force -Path "resources/views/filament/forms/components" | Out-Null

Write-Host "✅ Directories created" -ForegroundColor Green

# Install Composer packages
Write-Host "`n📦 Installing Composer packages..." -ForegroundColor Yellow
Write-Host "This may take a few minutes..." -ForegroundColor Gray

composer require "spatie/laravel-medialibrary:^11.0" --no-interaction
composer require "filament/spatie-laravel-media-library-plugin:^3.0" --no-interaction  
composer require "barryvdh/laravel-dompdf" --no-interaction

Write-Host "✅ Packages installed" -ForegroundColor Green

# Publish Media Library migrations
Write-Host "`n📤 Publishing Media Library assets..." -ForegroundColor Yellow
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations" --force

Write-Host "✅ Assets published" -ForegroundColor Green

# Create PDF preview component
Write-Host "`n📄 Creating PDF preview component..." -ForegroundColor Yellow
$pdfPreviewContent = @'
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
'@

Set-Content -Path "resources/views/filament/forms/components/pdf-preview.blade.php" -Value $pdfPreviewContent

Write-Host "✅ PDF preview component created" -ForegroundColor Green

# Create PDF template
Write-Host "`n📄 Creating PDF template..." -ForegroundColor Yellow
$pdfTemplateContent = @'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $correspondence->subject }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            direction: rtl; 
            text-align: right;
            padding: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .content { 
            margin: 20px 0; 
            line-height: 1.8; 
        }
        .meta { 
            background: #f5f5f5; 
            padding: 15px; 
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer { 
            margin-top: 50px;
            text-align: center; 
            font-size: 10px; 
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $correspondence->subject }}</h2>
        <p><strong>Reference:</strong> {{ $correspondence->ref_number }}</p>
        <p><strong>Date:</strong> {{ $correspondence->correspondence_date?->format('Y-m-d') }}</p>
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
        <p>Conference Management System</p>
    </div>
</body>
</html>
'@

Set-Content -Path "resources/views/pdf/correspondence.blade.php" -Value $pdfTemplateContent

Write-Host "✅ PDF template created" -ForegroundColor Green

# Create email template
Write-Host "`n📄 Creating email template..." -ForegroundColor Yellow
$emailTemplateContent = @'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .footer { text-align: center; padding: 15px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">{{ $correspondence->subject }}</h2>
        </div>
        
        <div class="content">
            <p><strong>Reference:</strong> {{ $correspondence->ref_number }}</p>
            <p><strong>Date:</strong> {{ $correspondence->correspondence_date?->format('Y-m-d') }}</p>
            <p><strong>Category:</strong> {{ ucwords(str_replace('_', ' ', $correspondence->category)) }}</p>
            
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">
            
            <div>
                {!! $correspondence->content !!}
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated email from the Conference Management System.</p>
            <p>&copy; {{ date('Y') }} Conference Organizing Committee</p>
        </div>
    </div>
</body>
</html>
'@

Set-Content -Path "resources/views/emails/correspondence.blade.php" -Value $emailTemplateContent

Write-Host "✅ Email template created" -ForegroundColor Green

# Run migrations
Write-Host "`n🗄️  Running migrations..." -ForegroundColor Yellow
php artisan migrate --force

Write-Host "✅ Migrations completed" -ForegroundColor Green

# Clear caches
Write-Host "`n🧹 Clearing caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear

Write-Host "✅ Caches cleared" -ForegroundColor Green

# Generate Shield permissions (if Shield is installed)
Write-Host "`n🛡️  Generating permissions..." -ForegroundColor Yellow
try {
    php artisan shield:generate --resource=CorrespondenceResource --force
    Write-Host "✅ Permissions generated" -ForegroundColor Green
}
catch {
    Write-Host "⚠️  Shield permissions skipped (run manually later)" -ForegroundColor Yellow
}

# Final instructions
Write-Host "`n✨ Setup Complete!" -ForegroundColor Cyan
Write-Host "`n📋 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Update Policy registration in app/Providers/AuthServiceProvider.php" -ForegroundColor White
Write-Host "2. Add reminder schedule to app/Console/Kernel.php" -ForegroundColor White
Write-Host "3. Run: php artisan shield:super-admin --user=1 --panel=admin" -ForegroundColor White
Write-Host "4. Visit /admin/correspondences to test the module" -ForegroundColor White
Write-Host "`nℹ️  See CORRESPONDENCE_SUMMARY.md for detailed instructions" -ForegroundColor Gray

Write-Host "`n🎉 All done! Happy coding!" -ForegroundColor Green
