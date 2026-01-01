<?php

namespace App\Models\Ums;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Company extends Structure

{
    use HasFactory;

    protected $table = 'user_companies';
    protected $fillable = array('uuid', 'company_id', 'user_id', 'name', 'registration_type', 'cac_number', 'cac_certificate', 'cac_certificate_confirmed', 'address', 'proof_of_address', 'address_confirmed', 'mermart_form', 'memart_confirmed', 'public_key', 'private_key', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'); 

    protected $hidden = array('created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    protected $casts = [
        'cac_certificate_confirmed' => 'boolean',
        'address_confirmed' => 'boolean',
        'memart_confirmed' => 'boolean',
        'status' => 'boolean',
    ];

    public function shareholders(){
        return $this->hasMany('App\Models\Ums\CompanyShareholder', 'company_id', 'uuid');
    }
}