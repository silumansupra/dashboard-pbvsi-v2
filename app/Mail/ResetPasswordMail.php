<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $resetUrl;
    public int $expiresInMinutes;
    public string $ipAddress;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $resetUrl, int $expiresInMinutes = 60, ?string $ipAddress = null)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
        $this->expiresInMinutes = $expiresInMinutes;
        $this->ipAddress = $ipAddress ?? request()->ip() ?? '127.0.0.1';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Reset Kata Sandi - Platform PBVSI',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_password',
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
