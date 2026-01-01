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

class PaymentSellerMail extends Mailable
{
    use Queueable, SerializesModels;
    public Transaction $transaction; 
    public User $buyer, $seller;
    public Payment $payment;

    public function __construct($transaction, $buyer, $seller, $payment)
    {
        $this->buyer = $buyer;
        $this->payment = $payment;
        $this->transaction = $transaction;
        $this->seller = $seller;
    }

    public function build()
    {
        $this->to($this->seller->email);
        return $this->subject( 'Payment of '.$this->payment->amount.' ['.$this->transaction->unique_code.'] from '.$this->buyer->first_name.' '.$this->buyer->last_name )
        //return $this->subject( 'Payment received !(is_null($this->seller->company)) ? $this->seller->company->name : $this->seller->first_name.' '.$this->seller->last_name.' Receipt of '.$this->payment->amount.' ['.$this->transaction->unique_code.']')
        ->view('mails.payments.seller');
    }
}
