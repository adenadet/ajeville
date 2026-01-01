<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Model;

use App\Models\Structure;

class Category extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'learn_course_categories';
    protected $fillable = array('name', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    public function sub_categories(){
    	return $this->hasMany('App\Models\Lms\SubCategory', 'category_id', 'id'); 
		}
	}