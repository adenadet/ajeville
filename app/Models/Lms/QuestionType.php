<?php

namespace App\Models\Learn;

use App\Models\Structure;

class QuestionType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'learn_question_types';
    protected $fillable = array('name', 'description');

}
