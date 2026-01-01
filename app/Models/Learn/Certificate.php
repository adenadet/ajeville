<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Certificate extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'learn_certificates';
    protected $fillable = array( 'certificate_code', 'course_id', 'user_id', 'score', 'scorable', 'grade', 'achieved_on', 'expiry_date');
	
    public function user(){
    	return $this->belongsTo('App\Models\User', 'id', 'user_id'); 
		}
	}