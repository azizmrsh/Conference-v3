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
