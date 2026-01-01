<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryStructureComponent extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInactive = 0;

    const CalcTypeFixed = 'fixed';
    const CalcTypePercentBasic = 'percent_basic';
    const CalcTypePercentGross = 'percent_gross';
    
    protected $primaryKey = 'id';
    protected $table = 'hrms_salary_structure_components';
    protected $fillable = array('salary_structure_id', 'name', 'label', 'calculation_type', 'amount', 'is_taxable', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function salary_structure(){
        return $this->belongsTo('App\Models\Hrms\SalaryStructure', 'salary_structure_id', 'id');
    }
    
    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
