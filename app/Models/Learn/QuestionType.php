<?php

namespace App\Models\Learn;

use App\Models\Structure;

class QuestionType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'question_types';
    protected $fillable = array('name', 'description');

}
