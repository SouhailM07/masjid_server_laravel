<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipRole extends Model
{
    //
    protected $fillable = ['name'];

    public function memberships(){
        return $this->hasMany(Membership::class);
    }
}
