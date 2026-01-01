<?php

namespace App\Models\Loans;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmationAction extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_confirmation_actions';
    protected $fillable = array('loan_id', 'action', 'summary', 'description', 'start_stage', 'end_stage', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
	}
}
