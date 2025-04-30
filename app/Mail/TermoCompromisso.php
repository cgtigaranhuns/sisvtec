<?php

namespace App\Mail;

use App\Models\VisitaTecnica;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TermoCompromisso extends Mailable
{
    use Queueable, SerializesModels;

    protected VisitaTecnica $visitaTecnica;
    protected $discente_id;

    /**
     * Create a new message instance.
     */
    public function __construct(VisitaTecnica $visitaTecnica, $discente_id)
    {
        $this->visitaTecnica = $visitaTecnica;
        $this->discente_id = $discente_id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Termo Compromisso',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.termoCompromisso',

            with: [
                'local' => $this->visitaTecnica->emp_evento,
                'dataSaida' => \Carbon\Carbon::parse($this->visitaTecnica->data_hora_saida)->format('d/m/Y H:i'),
                
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(public_path('storage\termos_compromisso\termo_compromisso_'.$this->visitaTecnica->id.'-'.$this->discente_id.'.pdf'))
                ->as('certificado.pdf')
                ->withMime('application/pdf'),
            ];
    }
}