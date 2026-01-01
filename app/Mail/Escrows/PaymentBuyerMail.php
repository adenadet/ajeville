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

class PaymentBuyerMail extends Mailable
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
        $this->to($this->buyer->email);
        return $this->subject( 'Payment Receipt of ₦'.number_format($this->payment->amount, 2).' ['.$this->transaction->unique_code.'] to '.(is_null($this->seller->company) ? $this->seller->first_name.' '.$this->seller->last_name : $this->seller->company->name))
        ->view('mails.payments.buyer');
    }
}
