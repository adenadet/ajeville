<?php

namespace App\Models\Ums;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerEmployer extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'customer_employers';
    protected $fillable = array('customer_id', 'user_id', 'name', 'employment_date', 'educational_level', 'employment_status', 'employer_lga_id', 'landmark', 'monthly_income', 'pay_day', 'pension_number', 'tax_no', 'employer_telephone_no', 'employer_sector_code', 'staff_id', 'employer_address', 'employer_city', 'employer_email', 'status', 'end_date', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function area(){
        return $this->belongsTo('App\Models\Area', 'employer_lga_id', 'id');
    }
}
