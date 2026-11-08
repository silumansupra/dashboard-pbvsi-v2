<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistrationVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = array_merge([
            'full_name' => 'Anggota PBVSI',
            'username' => '-',
            'password' => '-',
            'email' => '',
            'phone_number' => '-',
            'national_id' => '-',
            'role_title' => 'Wasit - PBVSI',
            'activation_url' => url('/login'),
            'cs_whatsapp' => '+62 817-0283-833',
            'cs_email' => 'support@pbvsi.or.id',
            'subject' => 'Registrasi Wasit - PBVSI',
        ], $data);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['subject'] ?? 'Registrasi Akun - Platform PBVSI',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registrasi_akun',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
