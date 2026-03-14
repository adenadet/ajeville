<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAnalyte extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_service_analytes';
    protected $fillable = array('service_id', 'analyte_id', 'created_at', 'updated_at', 'deleted_at');

    public function analyte(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Analyte', 'analyte_id', 'id');
    }

    public function service(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Service', 'service_id', 'id');
    }
}
