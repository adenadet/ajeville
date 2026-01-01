<?php

namespace App\Models\Loans;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disbursement extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_disbursements';
    protected $fillable = array( 'loan_id', 'amount', 'payment_date', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
}
