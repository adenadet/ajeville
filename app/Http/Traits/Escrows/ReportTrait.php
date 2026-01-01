<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\General\FileManagerTrait;
use Illuminate\Support\Facades\Mail;
trait ReportTrait {
    use FileManagerTrait;

    public function report_transaction_monthly_transaction_summary(){}

}