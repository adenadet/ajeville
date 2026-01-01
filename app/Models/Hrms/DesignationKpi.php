<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationKpi extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'hrms_designation_kpis';
    protected $fillable = ['designation_id', 'title', 'description', 'max_score', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function designation() {
        return $this->belongsTo(Designation::class);
    }
}
