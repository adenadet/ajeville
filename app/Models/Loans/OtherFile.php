<?php

namespace App\Models\Loans;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherFile extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_guarantors';
    protected $fillable = array('account_id', 'file_comment', 'file_name', 'sources', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by',  'id'); 
	}
}
