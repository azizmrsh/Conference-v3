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
