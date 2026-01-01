<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Structure;

class File extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_account_files';
    protected $fillable = array('file_name', 'file_comment', 'source', 'file_type', 'description', 'status', 'loan_id', 'approved_by', 'approved_date', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function account(){
        return $this->belongsTo('App\Models\Loans\Account', 'loan_id', 'id');
    }
}
