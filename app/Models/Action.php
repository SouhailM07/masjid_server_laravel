<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    /** @use HasFactory<\Database\Factories\ActionFactory> */
    use HasFactory;

    protected $fillable = ['name','isPublic','description'];

    public function roles(){
        return $this->belongsToMany(Role::class,'role_action');
    }

    
}
