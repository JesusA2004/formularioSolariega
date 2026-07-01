<?php

namespace App\Mail;

use App\Models\Request as BuzonRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRequestNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public BuzonRequest $request,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nueva solicitud recibida en el buzón - {$this->request->folio}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.requests.new-request',
            with: [
                'request' => $this->request,
                'panelUrl' => route('solicitudes.show', $this->request),
            ],
        );
    }
}
