<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExperience extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'hrms_user_experiences';
    protected $fillable = array('user_id', 'institution_name', 'institution_location', 'institution_job_description', 'start_date',  'end_date');

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
