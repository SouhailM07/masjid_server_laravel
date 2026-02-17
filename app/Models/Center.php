<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    /** @use HasFactory<\Database\Factories\CenterFactory> */
    use HasFactory;

    protected $fillable = ["name","logo","city","wilaya","primaryColor","secondaryColor","accentColor","type"];

    public function contacts(){
        return $this->hasMany(Contact::class);
    }

    public function socials(){
        return $this->hasMany(Social::class);
    }

    public function users(){
        return $this->belongsToMany(User::class,"memberships")
                    ->withPivot("membership_role_id")
                    ->as('membership')
                    ->using(Membership::class);
    }

    public function owner(){
        $ownerRoleId = MembershipRole::where("name",'owner')->value('id');
        return $this->belongsToMany(User::class,'memberships')
                    ->withPivot('membership_role_id',$ownerRoleId)
                    ->as("membership")
                    ->using(Membership::class);
    }

}
