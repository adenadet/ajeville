<?php

namespace App\Models\Ums;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'user_social_media';
    protected $fillable = array('user_id', 'facebook_url', 'twitter_url', 'linkedin_url', 'instagram_url', 'created_by', 'updated_by', 'deleted_by', 'confirmed_at', 'created_at', 'updated_at', 'deleted_at'); 

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    

}
