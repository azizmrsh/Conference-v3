<?php

namespace App\Mail;

use App\Models\Correspondence;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CorrespondenceSent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Correspondence $correspondence,
        public ?string $additionalMessage = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->correspondence->ref_number.' - '.$this->correspondence->sender_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.correspondence',
            with: [
                'correspondence' => $this->correspondence,
                'additionalMessage' => $this->additionalMessage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach PDF if exists
        $pdfMedia = $this->correspondence->getFirstMedia('pdf');
        if ($pdfMedia) {
            $attachments[] = Attachment::fromPath($pdfMedia->getPath())
                ->as($pdfMedia->file_name)
                ->withMime('application/pdf');
        }

        // Attach other files from attachments collection
        foreach ($this->correspondence->getMedia('attachments') as $media) {
            $attachments[] = Attachment::fromPath($media->getPath())
                ->as($media->file_name)
                ->withMime($media->mime_type);
        }

        return $attachments;
    }
}
