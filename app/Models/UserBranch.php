<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBranch extends Structure {

    protected $primaryKey = 'id';
    protected $table = 'user_branches';
    protected $fillable = array('user_id', 'name', 'address', 'phone', 'email', 'relationship');

    public function branch(){
        return $this->belongsTo('App\Models\Operations\Branch', 'branch_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}