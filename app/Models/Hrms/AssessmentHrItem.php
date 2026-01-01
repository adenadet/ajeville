<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentHrItem extends Structure
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'hrms_assessment_hr_items';
    protected $fillable = ['title', 'description', 'max_score', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];
}
