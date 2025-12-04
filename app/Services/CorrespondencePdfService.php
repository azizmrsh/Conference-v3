<?php

namespace App\Services;

use App\Models\Correspondence;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class CorrespondencePdfService
{
    /**
     * Generate PDF for a correspondence using Browsershot
     *
     * @param Correspondence $correspondence
     * @return string Path to the generated PDF
     */
    public function generatePdf(Correspondence $correspondence): string
    {
        // Generate HTML from blade template
        $html = view('pdf.correspondence', [
            'correspondence' => $correspondence,
        ])->render();

        // Generate filename
        $filename = $this->generateFilename($correspondence);
        $path = 'pdf/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Generate PDF using Browsershot
        Browsershot::html($html)
            ->setOption('landscape', false)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->save($fullPath);

        // Optionally attach to correspondence media library
        if (!$correspondence->getFirstMedia('pdf')) {
            $correspondence
                ->addMedia($fullPath)
                ->toMediaCollection('pdf');
        }

        return $path;
    }

    /**
     * Generate a unique filename for the PDF
     *
     * @param Correspondence $correspondence
     * @return string
     */
    protected function generateFilename(Correspondence $correspondence): string
    {
        $refNumber = str_replace(['/', '\\', ' '], '-', $correspondence->ref_number);
        $timestamp = now()->format('Ymd_His');

        return "correspondence_{$refNumber}_{$timestamp}.pdf";
    }

    /**
     * Get the PDF path for a correspondence
     *
     * @param Correspondence $correspondence
     * @return string|null
     */
    public function getPdfPath(Correspondence $correspondence): ?string
    {
        $media = $correspondence->getFirstMedia('pdf');

        if ($media) {
            return $media->getPath();
        }

        return null;
    }

    /**
     * Delete the PDF for a correspondence
     *
     * @param Correspondence $correspondence
     * @return bool
     */
    public function deletePdf(Correspondence $correspondence): bool
    {
        $media = $correspondence->getFirstMedia('pdf');

        if ($media) {
            $media->delete();
            return true;
        }

        return false;
    }
}
