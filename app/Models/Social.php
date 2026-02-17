<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    /** @use HasFactory<\Database\Factories\SocialFactory> */
    use HasFactory;

    protected $fillable = ["center_id","value",'type'];

    public function center(){
        return $this->belongsTo(Center::class);
    }
}
