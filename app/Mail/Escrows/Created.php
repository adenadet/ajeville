<?php

namespace App\Mail\Escrows;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Created extends Mailable
{
    use Queueable, SerializesModels;
    public $transaction, $user;

    public function __construct($transaction, $user)
    {
        $this->transaction = $transaction;
        $this->user = $user;
    }

    public function build()
    {
        $this->to($this->user->email);
        return $this->subject('You have a pending Escrow Transaction')
        ->view('mails.escrows.transactions.initialization');
    }
}
