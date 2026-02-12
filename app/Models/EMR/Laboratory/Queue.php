<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $table = 'emr_laboratory_queues';

    protected $fillable = ['request_item_id', 'queue_type', 'priority', 'entered_at', 'exited_at',];

    public function request_item()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\RequestItem', 'request_item_id', 'id');
    }
}
