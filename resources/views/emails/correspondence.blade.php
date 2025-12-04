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
            
            @if($additionalMessage)
                <div style="background: #fef3c7; padding: 10px; margin: 15px 0; border-left: 4px solid #f59e0b;">
                    <p><strong>Additional Message:</strong></p>
                    <p>{{ $additionalMessage }}</p>
                </div>
            @endif
            
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
