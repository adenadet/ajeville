<?php
namespace App\Http\Traits\Loans;

use App\Http\Traits\General\FileTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Finance\AllBank;
use App\Models\Loans\Account;
use App\Models\Loans\AccountOfficer;
use App\Models\Loans\CreditScore;
use App\Models\Loans\File;
use App\Models\Loans\Guarantor;
use App\Models\Loans\GuarantorRequest;
use App\Models\Loans\Repayment;
use App\Models\Loans\Type;

use App\Mail\Guarantor\ConfirmMail;
use App\Mail\Guarantor\GuaranteedMail;
use App\Mail\Guarantor\RequestMail;
use App\Mail\Guarantor\ThanksMail;

use App\Mail\Loans\AssignmentMail;
use App\Mail\Loans\NewAccountOfficerMail;
use Illuminate\Support\Facades\Mail;

trait AccountTrait{
    use FileTrait, LogTrait;
    public function loan_account_assign_account_officer($account_id, $staff_id){
        $accounts = AccountOfficer::where('account_id', '=', $account_id)->get();

        foreach ($accounts as $account) {
            $account->status = 2;
            $account->deleted_by = auth('api')->id();
            $account->deleted_at = date('Y-m-d H:i:s');
            $account->save();
        }

        AccountOfficer::create([
            'account_id' => $account_id,
            'staff_id' => $staff_id,
            'status' => 1,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        $loan = Account::where('id', '=', $account_id)->with(['account_officer', 'user'])->first();
        Mail::to($loan->account_officer->email)->send(new AssignmentMail($loan));
        //Mail::to($loan->user->email)->send(new NewAccountOfficerMail($loan));

        return "Completed";
    }
    public function loan_account_create($data){}

    public function loan_account_delete(){}

    public function loan_account_get_all($type, $specific, $detailed, $paginated, $page){

    }

    public function loan_account_get_by($type, $specific, $detailed){
        
    }

    public function loan_account_update($data, $id){}

}