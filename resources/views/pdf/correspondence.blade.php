<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <title>{{ $correspondence->subject }}</title>

    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Traditional Arabic', 'Arabic Typesetting', 'Simplified Arabic', 'DejaVu Sans';
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            font-size: 15pt;
            line-height: 2.2;
        }

        /* ===== Header ===== */
        .header-image {
            width: 100%;
            text-align: center;
        }

        .header-image img {
            width: 100%;
            height: auto;
        }

        /* ===== Page Content ===== */
        .page {
            padding: 25mm 20mm 20mm 20mm;
            position: relative;
        }

        /* ===== Document Meta ===== */
        .document-meta {
            position: absolute;
            top: 35mm;
            left: 20mm;
            text-align: right;
            font-size: 12pt;
            line-height: 1.8;
        }

        /* ===== Subject ===== */
        .subject {
            text-align: center;
            font-size: 17pt;
            margin: 40mm 0 20mm 0;
            font-weight: bold;
        }

        /* ===== Recipient ===== */
        .recipient {
            margin-bottom: 20px;
        }

        /* ===== Content ===== */
        .content {
            text-align: justify;
        }

        /* ===== Signature ===== */
        .signature {
            margin-top: 50px;
            text-align: center;
            font-size: 14pt;
        }

        /* ===== Footer ===== */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            line-height: 1.6;
            color: #000;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header-image">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(base_path('documentation/documentHeaderAr.png'))) }}">
    </div>

    <div class="page">

        <!-- Meta -->
        <div class="document-meta">
            <div>الرقم: {{ $correspondence->ref_number }}</div>
            <div>التاريخ: {{ $correspondence->correspondence_date?->locale('ar')->translatedFormat('j F Y') }}</div>
            <div>الموافق: {{ $correspondence->created_at->format('Y/m/d') }}</div>
        </div>

        <!-- Subject -->
        @if($correspondence->subject)
            <div class="subject">
                {{ $correspondence->subject }}
            </div>
        @endif

        <!-- Recipient -->
        @if($correspondence->direction === 'outgoing' && $correspondence->recipient_entity)
            <div class="recipient">
                {{ $correspondence->recipient_entity }}
            </div>
        @endif

        <!-- Content -->
        <div class="content">
            {!! $correspondence->content !!}
        </div>

        <!-- Signature -->

        <!-- Footer -->
        <div class="footer">
            <div>ص.ب: 950361 – عمّان 11195 الأردن</div>
            <div>هاتف: 5344570 (009626) – فاكس: 5344981 (009626)</div>
            <div>www.aalalbayt.org – aalalbayt@aalalbayt.org</div>
        </div>

    </div>
</body>
</html>
