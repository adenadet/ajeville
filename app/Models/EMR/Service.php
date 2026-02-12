<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Service extends Structure
{
    public const StatusActive = 1;
    public const StatusInactive = 0;    
    protected $primaryKey = 'id';
    protected $table = 'emr_services';
    protected $fillable = array('id', 'item_id', 'service_type_id', 'referenceable_type', 'referenceable_id', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function item(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function reference()
    {
        return $this->morphTo('referenceable');
    }

    public function service_type(){
        return $this->belongsTo('App\Models\EMR\Settings\ServiceType', 'service_type_id', 'id');
    }
    
    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    protected static function referenceMap(): array
    {
        return [
            'Laboratory'     => \App\Models\EMR\Laboratory\Service::class,
            'Radiology'      => \App\Models\EMR\Radiology\Service::class,
            'Physiotherapy'  => \App\Models\EMR\Physiotherapy\Service::class,
            'Dialysis'       => \App\Models\EMR\Dialysis\Service::class,
        ];
    }
}