<?php

namespace App\Models\Loans;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckListItem extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_checklist_items';
    protected $fillable = array('loan_id', 'requirement_id', 'status', 'comment', 'loan_officer_id');


    public function requirement(){
        return $this->belongsTo('App\Models\Loans\Requirement', 'requirement_id', 'id');
    }

    public function account(){
        return $this->belongsTo('App\Models\Loans\Account', 'loan_id', 'id');
    }

    public function account_officer(){
        return $this->belongsTo('App\Models\User', 'loan_officer_id', 'id');
    }
}
