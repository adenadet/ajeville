<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'loan_repayments';
    protected $fillable = array('loan_id', 'amount', 'payment_mode_id', 'bank_id', 'payment_date', 'description', 'status', 'logged_by', 'logged_date', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');
}
