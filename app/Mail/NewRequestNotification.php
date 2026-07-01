<?php

namespace App\Mail;

use App\Models\Request as BuzonRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewRequestNotification extends Mailable
{
    public function __construct(
        public BuzonRequest $request,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo mensaje recibido - Buzón Solariega',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requests.new-request',
            with: [
                'request' => $this->request,
                'panelUrl' => route('solicitudes.show', $this->request),
            ],
        );
    }
}
