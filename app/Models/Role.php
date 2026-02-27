<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    //
    protected $fillable = ["name",'isPublic','description'];

    // ! relations
    public function users(){
        return $this->hasMany(User::class);
    }

    public function actions(){
        return $this->belongsToMany(Action::class,'role_action')
                    ->withPivot(['create','read','update','delete'])
                    ->withTimestamps();
    }
}
