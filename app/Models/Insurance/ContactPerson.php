<?php

namespace App\Models\Insurance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'hmo_provider_contact_persons';
    protected $fillable = array('provider_id', 'name', 'phone', 'email', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function provider(){
    	return $this->belongsTo('App\Models\Insurance\Provider', 'id', 'provider_id');
	}
}
