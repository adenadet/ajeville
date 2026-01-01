<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    const StatusDraft = 0; //Generally created by HR or Line Manager
    const StatusInProgress = 1; //Employee Completed
    const StatusCompleted = 2; //Finalized by Line Manager
    protected $primaryKey = 'id';
    protected $table = 'hrms_applicants';
    protected $fillable = [
        'user_id', 'city', 'cv', 'status', 'recruited', 'category_id', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function period() {
        return $this->belongsTo(AssessmentPeriod::class, 'assessment_period_id');
    }

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function line_manager() {
        return $this->belongsTo(Employee::class, 'line_manager_id');
    }

    public function answers() {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function comments() {
        return $this->hasMany(AssessmentComment::class);
    }
}
