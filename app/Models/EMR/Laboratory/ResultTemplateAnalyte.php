<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultTemplateAnalyte extends Structure
{
    use HasFactory;

    public function referenceRanges()
    {
        return $this->hasMany('App\Models\EMR\Laboratory\ReferenceRange', 'analyte_id', 'analyte_id');
    }
}
