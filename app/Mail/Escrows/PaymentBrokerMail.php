<?php

namespace App\Mail\QuickPay;

use App\Models\Escrows\Payment;
use App\Models\Escrows\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentBrokerMail extends Mailable
{
    use Queueable, SerializesModels;
    public Transaction $transaction; 
    public User $user;
    public Payment $payment;

    public function __construct($transaction, $user, $payment)
    {
        $this->payment = $payment;
        $this->transaction = $transaction;
        $this->user = $user;
    }

    public function build()
    {
        $this->to($this->user->email);
        return $this->subject( 'Payment of '.$this->payment->amount.' ['.$this->transaction->unique_code.']')
        ->view('mails.payments.broker');
    }
}