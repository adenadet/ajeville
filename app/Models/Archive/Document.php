<?php

namespace App\Models\Archive;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Structure
{
    use HasFactory;

    protected $fillable = array('patient_id', 'file_name', 'file_type', 'file_path', 'category_id', 'created_by', 'updated_by',  'deleted_by', 'created_at', 'updated_at',  'deleted_at');

    public function category()
    {
        return $this->belongsTo('App\Models\Archive\Category', 'category_id', 'id');
    }

    public function creater()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function patient()
    {
        return $this->belongsTo('App\Models\EMR\Patient', 'patient_id', 'id');
    }

    public function updater()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
