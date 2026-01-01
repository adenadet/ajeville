<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Type extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_types';
    protected $fillable = array('name', 'category_id', 'matrix_id', 'percentage', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function matrix(){
        return $this->belongsTo('App\Models\Loans\ConfirmationMatrix', 'matrix_id', 'id');
    }

    public function requirements(){
        return $this->hasManyThrough('App\Models\Loans\Requirement', 'App\Models\Loans\TypeRequirement', 'loan_type_id', 'id', 'id', 'requirement_id', );
    }
}
