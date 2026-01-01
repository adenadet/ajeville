<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeRequirement extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'loan_type_requirements';
    protected $fillable = array('loan_type_id', 'requirement_id');

    public function requirement(){
        return $this->belongsTo('App\Models\Loans\Requirement', 'requirement_id', 'id');
    }
}
