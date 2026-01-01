<?php

namespace App\Mail\Leave;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupervisorInfoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave_request;
    public $employee;
    public $line_manager;

    public function __construct($leave_request, $employee, $line_manager)
    {
        $this->leave_request = $leave_request;
        $this->employee = $employee;
        $this->line_manager = $line_manager;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Supervisor Info Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}