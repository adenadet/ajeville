<?php

namespace App\Mail\Ums;

use App\Models\Ums\UserOTP;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user; 
    public UserOTP $otp;
    public function __construct(User $user, UserOTP $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('app.otp_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mails.ums.otp',
        );
    }
}
