<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'hrms_payroll_items';
    protected $fillable = array('payroll_period_id', 'employee_id', 'gross_pay', 'total_allowances', 'total_deductions', 'net_pay', 'breakdown', 'confirmed_by', 'confirmed_at', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    protected $casts = [
        'breakdown' => 'array'
    ];

    public function confirmer(){
        return $this->belongsTo('App\Models\User', 'confirmed_by', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function employee(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'employee_id', 'id');
    }

    public function payroll_period(){
        return $this->belongsTo('App\Models\Hrms\PayrollPeriod', 'period_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
