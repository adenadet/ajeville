<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditScore extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'loan_account_credit_scores';
    protected $fillable = array('loan_id', 'credit_score', 'product_id', 'response', 'validation_type', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
    public function product(){
        return $this->hasOne('App\Models\Finance\BureauProduct', 'id', 'product_id');
    }

}
