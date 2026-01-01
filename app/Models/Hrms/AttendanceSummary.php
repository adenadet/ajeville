<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'hrms_employee_attendance_summaries';
    protected $fillable = array('employee_id', 'date', 'shift_id', 'clock_in', 'clock_out', 'total_hours', 'late_by_minutes', 'early_by_minutes', 'overtime', 'is_absent', 'notes', 'created_at', 'updated_at', 'deleted_at');
    protected $casts = [
        'clock_in'      => 'datetime',
        'clock_out'     => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];
}
