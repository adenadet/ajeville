<?php

namespace App\Mail\Sales;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $invoice;   // assume you pass an Invoice model/DTO

    public function __construct($customer, $invoice)
    {
        $this->customer = $customer;
        $this->invoice  = $invoice;
    }

    public function build()
    {
        // 1) Render the PDF from the Blade template
        $pdf = Pdf::loadView(
            'emails.attachment.sales.invoice',
            ['customer' => $this->customer, 'invoice' => $this->invoice]
        );

        // 2) Build and return the mail
        return $this->subject("Your Invoice #{$this->invoice->uuid}")
                    ->view('emails.attachment.sales.invoice') // email body (can be same Blade)
                    ->attachData(
                        $pdf->output(),
                        "invoice-{$this->invoice->number}.pdf",
                        ['mime' => 'application/pdf']
                    );
    }
}
