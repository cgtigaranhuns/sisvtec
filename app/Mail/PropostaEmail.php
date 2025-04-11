<?php

namespace App\Mail;

use App\Models\VisitaTecnica;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropostaEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected VisitaTecnica $visitaTecnica)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Proposta Cadastrada com Sucesso',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        $local = $this->visitaTecnica->emp_evento;
        $dataSaida = $this->visitaTecnica->data_hora_saida;
        $dataSaida = \Carbon\Carbon::parse($dataSaida)->format('d/m/Y H:i');
        $dataRetorno = $this->visitaTecnica->data_hora_retorno;
        $dataRetorno = \Carbon\Carbon::parse($dataRetorno)->format('d/m/Y H:i');
        $responsavel = $this->visitaTecnica->professor->name;
        $turma = $this->visitaTecnica->turma->nome;
        $estado = $this->visitaTecnica->estado->nome;
        $cidade = $this->visitaTecnica->cidade->nome;


        return new Content(
            view: 'email.propostaEmail',

            with: [
                'local' => $local,
                'dataSaida' => $dataSaida,
                'dataRetorno' => $dataRetorno,
                'responsavel' => $responsavel,
                'turma' => $turma,
                'estado' => $estado,
                'cidade' => $cidade,
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
        return [];
    }
}
