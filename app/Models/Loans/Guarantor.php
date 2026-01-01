<?php

namespace App\Models\Loans;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guarantor extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_guarantors';
    protected $fillable = array('loan_id', 'request_id', 'bvn', 'bvn_status', 'bvn_confirmed_by', 'bvn_confirmed_at', 'nin', 'nin_status', 'nin_confirmed_by', 'nin_confirmed_at', 'title', 'first_name', 'middle_name', 'last_name', 'relationship', 'email', 'phone', 'dob', 'nationality_id', 'employer', 'employer_address', 'employer_address_confirmed_by', 'employer_address_confirmed_at', 'employer_address_status', 'residential_address', 'residential_address_confirmed_by', 'residential_address_confirmed_at', 'residential_address_status', 'marital_status', 'gender', 'address', 'employer_phone', 'employer_email', 'status', 'description', 'net_income', 'guarantor_passport', 'address_proof', 'valid_id', 'passport', 'guarantor_signature', 'guarantor_date', 'approve_bvn_by', 'approve_bvn_date', 'approve_bvn_status', 'approve_bvn_remark', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');

    public function employer_address_verification(){
        return $this->belongsTo('App\Models\Loans\GuarantorAddressVerification', 'guarantor_id', 'id');
    }
    
    public function loan(){
        return $this->belongsTo('App\Models\Loans\Account', 'loan_id', 'id');
    }

    public function residential_address_verification(){
        return $this->belongsTo('App\Models\Loans\GuarantorAddressVerification', 'guarantor_id', 'id');
    }
}
