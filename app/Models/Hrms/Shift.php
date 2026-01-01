<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $table = 'hrms_shifts';
    protected $fillable = array('name', 'start_time', 'end_time', 'overnight', 'created_at', 'updated_at', 'deleted_at');
    protected $casts = [
        'start_time'    => 'time',
        'end_time'      => 'time',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];
}