<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prayer extends Model
{
    /** @use HasFactory<\Database\Factories\PrayerFactory> */
    use HasFactory;
    protected $fillable = ["time","center_id","type"];

    public function center(){
        return $this->belongsTo(Center::class);
    }
}
