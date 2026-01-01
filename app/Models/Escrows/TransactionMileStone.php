<?php

namespace App\Models\Escrows;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionMileStone extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'escrow_transaction_mile_stones';
    protected $fillable = array('description', 'transaction_id', 'completion_level', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

}
