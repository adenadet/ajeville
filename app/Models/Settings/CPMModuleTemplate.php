<?php 

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPMModuleTemplate extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'setting_cpm_module_templates';
    protected $fillable = array('name', 'module_id', 'loan_type_id', 'details', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
		return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}
    
	public function updater(){
		return $this->belongsTo('App\Models\User', 'updated_by', 'id');
	}
}
