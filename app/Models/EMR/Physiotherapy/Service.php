<?php

namespace App\Models\EMR\Physiotherapy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Service extends Structure
{
    public const StatusActive = 1;
    public const StatusInactive = 0;    
    protected $primaryKey = 'id';
    protected $table = 'emr_physiotherapy_services';

}