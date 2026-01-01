<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAnswer extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id';
    protected $table = 'hrms_assessment_answers';

    protected $fillable = ['assessment_id', 'item_type', 'item_id', 'employee_score',  'line_manager_score', 'line_manager_comment', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at' ];

    public function assessment() {
        return $this->belongsTo(Assessment::class);
    }

    public function item() {
        // Helper to load item model dynamically
        return $this->morphTo(null, 'item_type', 'item_id');
        // Note: because item_type is 'hr' or 'kpi', you could map that manually in accessors.
    }
}
