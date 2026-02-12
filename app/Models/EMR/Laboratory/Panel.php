<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    use HasFactory;

    protected $table = 'emr_laboratory_panels';

    protected $fillable = ['name', 'service_id', 'category_id', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function tests()
    {
        return $this->belongsToMany('App\Models\EMR\Laboratory\Service', 'emr_laboratory_panel_tests', 'panel_id', 'service_id')->withPivot('sort_order');
    }
}
