<?php

namespace App\Models\Sales;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderApproval extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'approval_sales_orders';
    protected $fillable = array('so_id', 'approved_by', 'remark', 'decision', 'created_at', 'updated_at', 'deleted_at');

    public function order(){
        return $this->belongsTo('App\Models\Sales\Order', 'so_id', 'id');
    }
}
