<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentEmployeeTarget extends Model
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInactive = 0;
    protected $primaryKey = 'id';
    protected $table = 'hrms_assessments';

    protected $fillable = [
        'assessment_period_id', 'designation_id', 'employee_id', 'title', 'description', 'max_score', 'due_date', 'order', 'status', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'
    ];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'assessment_period_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
