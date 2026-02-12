<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specimen extends Model
{
    use HasFactory;

    protected $table = 'emr_laboratory_specimens';

    protected $fillable = ['request_item_id', 'specimen_type_id', 'bottle_type_id', 'barcode', 'status', 'collected_by', 'collected_at', 'received_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function requestItem(){
        return $this->belongsTo('App\Models\EMR\Laboratory\RequestItem', 'request_item_id', 'id');
    }

    public function rejection(){
        return $this->hasOne('App\Models\EMR\Laboratory\SpecimenRejection', 'specimen_id', 'id');
    }
}
