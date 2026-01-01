<?php

namespace App\Mail\Ums;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Confirm Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mails.ums.password_reset_confirm',
        );
    }
}
