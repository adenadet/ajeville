<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Model;

class Bank extends Structure{
    protected $primaryKey = 'id';
    protected $table = 'finance_all_banks';
    protected $fillable = array('bank_name', 'deleted_at'); 
}