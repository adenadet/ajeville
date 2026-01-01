<?php

namespace App\Models\EMR\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_assessment_items';
    protected $fillable = array('name', 'description', 'created_by', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

    public function assessments(){
    	return $this->hasManyThrough('App\Models\EMR\Assessment\Item','App\Models\EMR\Assessment\TypeItem');
	}
}
