<?php

namespace App\Mail;

use App\Models\QuickResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuickResponseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $quickResponse;

    /**
     * Create a new message instance.
     */
    public function __construct(QuickResponse $quickResponse)
    {
        $this->quickResponse = $quickResponse;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alalayang Agila Emergency Alert: '. $this->quickResponse->libHelp->name .' of kuya ' . $this->quickResponse->user->fullname,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.quick_response_notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
