<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['season_id', 'name', 'contact_email'];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}