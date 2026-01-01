<?php

namespace App\Mail\Loans;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ERMConfirmMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $loan;

    public function __construct($loan)
    {
        $this->loan = $loan;
    }

    public function build()
    {
        return $this->subject('Loan Confirmed Notification')
        ->view('mails.loans.confirm_erm');
    }
}
