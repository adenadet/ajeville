<?php

namespace App\Models\Ums;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddressVerification extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'customer_address_verifications';
    protected $fillable = array( 'address_id', 'alternate_address', 'description', 'location_ease', 'met_with', 'met_with_name', 'met_with_relations', 'met_with_phone', 'remarks', 'visit_update', 'visit_date', 'visit_date_2', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'); 

    public function address(){
        return $this->belongsTo('App\Models\Ums\CustomerAddress', 'address_id', 'id');
    }
}
