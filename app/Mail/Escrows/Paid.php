<?php

namespace App\Mail\Escrows;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Escrows\Transaction;
use App\Models\User;

class Paid extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction; 
    public User $user;

    public function __construct($transaction, $user)
    {
        $this->transaction = $transaction;
        $this->user = $user;
    }

    public function build()
    {
        $this->to($this->user->email);
        return $this->subject( 'Your Escrow Transaction has been paid')
        ->view('mails.escrows.transactions.paid');
    }
}
