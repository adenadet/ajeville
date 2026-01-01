<?php
namespace App\Http\Traits\Coop;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Coop\Cooperator;
use App\Models\Coop\Guarantor;
use Illuminate\Support\Facades\Mail;
trait GuarantorTrait {
    use FileManagerTrait, LogTrait;

    public function coop_guarantor_create($data){
        $guarantor = new Guarantor();
        $guarantor->user_id = $data['user_id'] ?? auth('api')->id(); 
        $guarantor->loan_id = $data['loan_id'] ?? null; 
        $guarantor->guarantor_id = $data['guarantor_id'] ?? null; 
        $guarantor->status_date = date('Y-m-d');
        $guarantor->status = 0;
        $guarantor->save();
    }
}