<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentPeriod extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInactive = 0;
    protected $primaryKey = 'id';
    protected $table = 'hrms_assessment_periods';

    protected $fillable = ['name', 'start_date', 'end_date', 'status', 'notes', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function assessments() {
        return $this->hasMany(Assessment::class);
    }
}
