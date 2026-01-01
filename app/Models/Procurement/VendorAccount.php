<?php

namespace App\Models\Procurement;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAccount extends Structure
{
    use HasFactory;

    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'procurement_vendor_accounts';
    protected $fillable = array('vendor_id', 'bank_id', 'account_name', 'account_number', 'status', 'created_at', 'updated_at', 'deleted_at');

    public function bank(){
        return $this->belongsTo('App\Models\Finance\Bank', 'bank_id', 'id');
    }

    public function vendor(){
        return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }

}
