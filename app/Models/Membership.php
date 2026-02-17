<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    //
    protected $fillable = ['user_id',"center_id","membership_role_id"];

    public function centers(){
        return $this->belongsTo(Center::class);
    }

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function membershipRole(){
        return $this->belongsTo(MembershipRole::class);
    }
}
