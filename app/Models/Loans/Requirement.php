<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Requirement extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_requirement_checklist_items';
    protected $fillable = array('name', 'type', 'rate', 'expires', 'attachment', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
}