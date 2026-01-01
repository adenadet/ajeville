<?php

namespace App\Mail\Escrows;

use App\Models\Escrows\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class Accepted extends Mailable
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
        return $this->subject( 'Your pending Escrow Transaction has been accepted')
        ->view('mails.escrows.transactions.accepted');
    }
}
