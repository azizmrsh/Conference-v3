<div class="p-4">
    @if(str_starts_with($record->mime_type, 'image/'))
        <div class="flex justify-center">
            <img src="{{ $record->getUrl() }}" 
                 alt="{{ $record->file_name }}" 
                 class="max-w-full max-h-[70vh] rounded-lg shadow-lg"
            >
        </div>
    @elseif($record->mime_type === 'application/pdf')
        <div class="w-full h-[70vh]">
            <iframe src="{{ $record->getUrl() }}" 
                    class="w-full h-full rounded-lg border-0"
                    title="{{ $record->file_name }}">
            </iframe>
        </div>
    @else
        <div class="text-center py-8">
            <x-filament::icon 
                icon="heroicon-o-document" 
                class="w-16 h-16 mx-auto text-gray-400"
            />
            <p class="mt-4 text-gray-600">Preview not available for this file type</p>
            <p class="text-sm text-gray-500">{{ $record->mime_type }}</p>
        </div>
    @endif

    <div class="mt-4 pt-4 border-t border-gray-200">
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="font-medium text-gray-500">File Name</dt>
                <dd class="mt-1 text-gray-900">{{ $record->file_name }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Size</dt>
                <dd class="mt-1 text-gray-900">{{ number_format($record->size / 1024, 2) }} KB</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Type</dt>
                <dd class="mt-1 text-gray-900">{{ $record->mime_type }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Uploaded</dt>
                <dd class="mt-1 text-gray-900">{{ $record->created_at->diffForHumans() }}</dd>
            </div>
        </dl>
    </div>
</div>
