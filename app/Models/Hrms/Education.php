<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Structure
{
    use HasFactory;

    const StatusVerified = 100;
    const StatusUnverified = 1;
    const StatusVerificationProcessing = 50;
    const StatusVerificationFailed = 40;

    protected $primaryKey = 'id';
    protected $table = 'hrms_user_educations';
    protected $fillable = array('user_id', 'qualification_id', 'details', 'institution', 'address', 'start_month', 'end_month', 'file', 'file_type', 'status', 'created_by', 'updated_by', 'deleted_by', 'deleted_at');
    
    public function creator(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    } 
    
    public function qualification(){
        return $this->belongsTo('App\Models\Hrms\Qualification', 'qualification_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
    
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
