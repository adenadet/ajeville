<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultReview extends Structure
{
    use HasFactory;

    protected $table = 'emr_laboratory_result_reviews';

    protected $fillable = ['result_id', 'reviewed_by', 'comment', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function result(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Result', 'result_id', 'id');
    }
}
