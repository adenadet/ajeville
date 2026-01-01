<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPMModule extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'setting_cpm_modules';
    protected $fillable = array('name', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
		return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

	public function templates(){
		return $this->hasMany('App\Models\Settings\CPMModuleTemplate', 'module_id', 'id');
	}

	public function updater(){
		return $this->belongsTo('App\Models\User', 'updated_by', 'id');
	}
}
