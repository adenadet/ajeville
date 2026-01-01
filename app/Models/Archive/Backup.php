<?php

namespace App\Models\Archive;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = ['backup_path', 'backup_date', 'status', 'created_by', 'updated_by', 'deleted_by'];

    public function creater()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
