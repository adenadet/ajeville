<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmationMatrixItem extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'loan_confirmation_matrix_items';
    protected $fillable = array('matrix_id', 'role_id', 'stage_number', 'created_at', 'updated_at', 'deleted_at');

    public function role(){
        return $this->belongsTo('App\Models\Ums\Role', 'role_id', 'id');
    }
}
