<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class TwoFactorOTP extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $name)
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Get email from .env config
        $fromEmail = config('mail.from.address');
        $fromName = config('mail.from.name', 'CHASTE');

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: 'Kode OTP Verifikasi 2FA - CHASTE',
            replyTo: [new Address($fromEmail, $fromName)],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor-otp',
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
