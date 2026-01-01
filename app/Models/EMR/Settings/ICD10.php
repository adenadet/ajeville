<?php

namespace App\Models\EMR\Settings;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICD10 extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_icd_ten_codes';
    protected $fillable = array('chapter_number', 'chapter_description', 'group_code', 'group_description', 'code', 'icd10_3_code_description', 'icd10_code', 'name');
}