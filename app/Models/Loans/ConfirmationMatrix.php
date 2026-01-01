<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmationMatrix extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'loan_confirmation_matrices';
    protected $fillable = array('name', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

}
