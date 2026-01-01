<?php

namespace App\Models\Sales;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Structure
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'sales_quotations';
    protected $fillable = ['uuid', 'customer_id', 'store_id', 'discount', 'logistics', 'quote_date', 'expiry_date', 'status', 'notes', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function customer()
    {
        return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
    }

    public function invoice()
    {
        return $this->hasOne('App\Models\Sales\Order', 'quotation_id', 'uuid');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function package(){
        return $this->belongsTo('App\Models\Procurement\PackageType', 'package_id', 'id');
    }

    public function quotation_items()
    {
        return $this->hasMany('App\Models\Sales\QuotationItem', 'quotation_id', 'uuid');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
